<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\Residents;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MedicineRequestController
{
    public function index(Request $request)
    {
        $query = MedicineRequest::with(['medicine', 'resident', 'approvedByUser']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function ($q) use ($search) {
                $q->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"]);
            })->orWhereHas('medicine', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%");
            })->orWhere('notes', 'like', "%{$search}%");
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('request_date', '>=', $request->get('start_date'));
        }
        
        if ($request->filled('end_date')) {
            $query->where('request_date', '<=', $request->get('end_date'));
        }

        // Approval status filter (based on whether quantity_approved is set)
        if ($request->filled('approval_status')) {
            $status = $request->get('approval_status');
            if ($status === 'approved') {
                $query->whereNotNull('quantity_approved');
            } elseif ($status === 'pending') {
                $query->whereNull('quantity_approved');
            }
        }

        // Medicine category filter
        if ($request->filled('medicine_category')) {
            $query->whereHas('medicine', function ($q) use ($request) {
                $q->where('category', $request->get('medicine_category'));
            });
        }

        // Get statistics (cache for 5 minutes)
        $stats = [
            'total_requests' => Cache::remember('total_medicine_requests', 300, function() {
                return MedicineRequest::count();
            }),
            'total_approved' => Cache::remember('total_approved_medicine_requests', 300, function() {
                return MedicineRequest::whereNotNull('quantity_approved')->count();
            }),
            'total_pending' => Cache::remember('total_pending_medicine_requests', 300, function() {
                return MedicineRequest::whereNull('quantity_approved')->count();
            }),
            'this_month' => Cache::remember('this_month_medicine_requests', 300, function() {
                return MedicineRequest::whereMonth('request_date', now()->month)
                                   ->whereYear('request_date', now()->year)
                                   ->count();
            }),
        ];

        // Get unique medicine categories for filter dropdown
        $medicineCategories = Cache::remember('medicine_categories', 300, function() {
            return Medicine::distinct()->pluck('category')->sort()->values();
        });

        // Paginate with proper ordering
        $requests = $query->orderBy('request_date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('admin.medicine-requests.index', compact('requests', 'stats', 'medicineCategories'));
    }

    public function create(Request $request)
    {
        $medicines = Medicine::active()->orderBy('name')->get();
        $residents = Residents::orderByRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))")->get();
        $medicalRecords = MedicalRecord::with('resident')
            ->orderBy('consultation_datetime', 'desc')
            ->get();
        
        // Pre-select values if coming from medical record
        $selectedMedicalRecordId = $request->get('medical_record_id');
        $selectedResidentId = $request->get('resident_id');
        
        return view('admin.medicine-requests.create', compact('medicines', 'residents', 'medicalRecords', 'selectedMedicalRecordId', 'selectedResidentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'resident_id' => 'required|exists:residents,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'quantity_requested' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);

        // Prevent multiple ongoing medicine requests (quantity_approved is null or 0)
        if (MedicineRequest::where('resident_id', $validated['resident_id'])
                ->where(function($q){ $q->whereNull('quantity_approved')->orWhere('quantity_approved', 0); })
                ->exists()) {
            notify()->error('Resident already has a pending or unapproved medicine request. Complete it before creating a new one.');
            return back();
        }

        // Ensure sufficient stock
        if ($medicine->current_stock < (int) $validated['quantity_requested']) {
            notify()->error('Insufficient stock to fulfill this request.');
            return back();
        }

        // Get the logged-in user (nurse/health worker)
        $approvedBy = Session::get('user_id');

        // Auto-approve request and fulfill immediately
        $medicineRequest = MedicineRequest::create([
            'medicine_id' => $validated['medicine_id'],
            'resident_id' => $validated['resident_id'],
            'medical_record_id' => $validated['medical_record_id'] ?? null,
            'quantity_requested' => (int) $validated['quantity_requested'],
            'quantity_approved' => (int) $validated['quantity_requested'],
            'approved_by' => $approvedBy,
            'request_date' => now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Deduct stock and log transaction
        $medicine->current_stock -= (int) $validated['quantity_requested'];
        $medicine->save();

        MedicineTransaction::create([
            'medicine_id' => $medicine->id,
            'resident_id' => $validated['resident_id'],
            'medical_record_id' => $validated['medical_record_id'] ?? null,
            'transaction_type' => 'OUT',
            'quantity' => (int) $validated['quantity_requested'],
            'transaction_date' => now(),
            'prescribed_by' => $approvedBy,
            'notes' => ($validated['notes'] ? 'Auto-dispensed via request #'.$medicineRequest->id . ' - ' . $validated['notes'] : 'Auto-dispensed via request #'.$medicineRequest->id)
        ]);

        notify()->success('Request created and dispensed.');
        return redirect()->route('admin.medicine-requests.index');
    }

    public function approve(Request $request, MedicineRequest $medicineRequest)
    {
        // Since medicine requests are auto-approved on creation, this method handles additional approval logic
        // or can be used for bulk approval scenarios
        
        if (!$medicineRequest->quantity_approved) {
            // If somehow the request wasn't approved, approve it now
            $medicineRequest->update([
                'quantity_approved' => $medicineRequest->quantity_requested,
                'approved_by' => Session::get('user_id'),
            ]);
            
            notify()->success('Medicine request approved successfully.');
        } else {
            notify()->info('Medicine request was already approved.');
        }
        
        return back();
    }

    public function reject(Request $request, MedicineRequest $medicineRequest)
    {
        // For medicine requests, rejection might mean setting quantity_approved to 0
        // or adding a rejection note
        
        $medicineRequest->update([
            'quantity_approved' => 0,
            'notes' => ($medicineRequest->notes ? $medicineRequest->notes . ' - ' : '') . 'Rejected: ' . ($request->input('rejection_reason', 'No reason provided')),
        ]);
        
        notify()->success('Medicine request rejected successfully.');
        return back();
    }
}


