<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\Residents;
use App\Models\MedicineBatch;
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
        return view('admin.medicine-requests.create', compact('medicines', 'residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'resident_id' => 'required|exists:residents,id',
            'quantity_requested' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'privacy_consent' => 'required|accepted',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);
        $requestedQty = (int) $validated['quantity_requested'];

        // Calculate available stock from non-expired batches (more accurate than current_stock)
        $now = now()->toDateString();
        $availableStock = MedicineBatch::where('medicine_id', $medicine->id)
            ->where('remaining_quantity', '>', 0)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', $now);
            })
            ->sum('remaining_quantity');

        // Fallback: If no batches exist or all are expired, use current_stock and create a batch
        // This handles legacy medicines created before batch system or out-of-sync data
        if ($availableStock == 0 && $medicine->current_stock > 0) {
            // Create a batch from current_stock to sync the data
            MedicineBatch::create([
                'medicine_id' => $medicine->id,
                'batch_code' => null,
                'quantity' => (int) $medicine->current_stock,
                'remaining_quantity' => (int) $medicine->current_stock,
                'expiry_date' => null, // No expiry date for legacy stock
                'notes' => 'Auto-created batch to sync legacy stock',
            ]);
            $availableStock = (int) $medicine->current_stock;
        }

        // Ensure sufficient stock
        if ($availableStock < $requestedQty) {
            notify()->error('Insufficient stock to fulfill this request. Available: ' . $availableStock . ', Requested: ' . $requestedQty);
            return back();
        }

        // Get the logged-in user (nurse/health worker)
        $approvedBy = Session::get('user_id');

        // Auto-approve request and fulfill immediately
        $medicineRequest = MedicineRequest::create([
            'medicine_id' => $validated['medicine_id'],
            'resident_id' => $validated['resident_id'],
            'quantity_requested' => $requestedQty,
            'quantity_approved' => $requestedQty,
            'approved_by' => $approvedBy,
            'request_date' => now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Deduct stock from oldest non-expired batches first (FEFO)
        $remainingToDeduct = $requestedQty;

        $batches = MedicineBatch::where('medicine_id', $medicine->id)
            ->where('remaining_quantity', '>', 0)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', $now);
            })
            ->orderBy('expiry_date')   // earliest expiry first
            ->orderBy('created_at')    // then oldest batch
            ->lockForUpdate()
            ->get();

        $totalDeducted = 0;
        foreach ($batches as $batch) {
            if ($remainingToDeduct <= 0) {
                break;
            }

            $available = (int) $batch->remaining_quantity;
            if ($available <= 0) {
                continue;
            }

            $take = min($available, $remainingToDeduct);
            $batch->remaining_quantity -= $take;
            $batch->save();

            $remainingToDeduct -= $take;
            $totalDeducted += $take;
        }

        if ($remainingToDeduct > 0) {
            // Rollback: delete the request if we couldn't fulfill it
            $medicineRequest->delete();
            notify()->error('Insufficient batch stock to fulfill this request. Available: ' . $totalDeducted . ', Requested: ' . $requestedQty . '. Please check batch availability.');
            return back();
        }

        // Deduct from overall medicine stock
        $medicine->current_stock -= $totalDeducted;
        $medicine->save();

        MedicineTransaction::create([
            'medicine_id' => $medicine->id,
            'resident_id' => $validated['resident_id'],
            'transaction_type' => 'OUT',
            'quantity' => $totalDeducted,
            'transaction_date' => now(),
            'prescribed_by' => $approvedBy,
            'notes' => ($validated['notes']
                ? 'Auto-dispensed via request #'.$medicineRequest->id . ' - ' . $validated['notes']
                : 'Auto-dispensed via request #'.$medicineRequest->id)
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


