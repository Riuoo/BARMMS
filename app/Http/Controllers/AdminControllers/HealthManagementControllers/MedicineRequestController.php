<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\Residents;
use Illuminate\Http\Request;

class MedicineRequestController
{
    public function index(Request $request)
    {
        $query = MedicineRequest::with(['medicine', 'resident']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('medicine', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('request_date', 'desc')->paginate(15)->withQueryString();

        return view('admin.medicine-requests.index', compact('requests'));
    }

    public function create()
    {
        $medicines = Medicine::active()->orderBy('name')->get();
        $residents = Residents::orderBy('name')->get();
        return view('admin.medicine-requests.create', compact('medicines', 'residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'resident_id' => 'required|exists:residents,id',
            'quantity_requested' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);

        // Ensure sufficient stock
        if ($medicine->current_stock < (int) $validated['quantity_requested']) {
            notify()->error('Insufficient stock to fulfill this request.');
            return back();
        }

        // Auto-approve request and fulfill immediately
        $medicineRequest = MedicineRequest::create([
            'medicine_id' => $validated['medicine_id'],
            'resident_id' => $validated['resident_id'],
            'quantity_requested' => (int) $validated['quantity_requested'],
            'quantity_approved' => (int) $validated['quantity_requested'],
            'status' => 'approved',
            'approved_by' => 'System',
            'request_date' => now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Deduct stock and log transaction
        $medicine->current_stock -= (int) $validated['quantity_requested'];
        $medicine->save();

        MedicineTransaction::create([
            'medicine_id' => $medicine->id,
            'resident_id' => $validated['resident_id'],
            'transaction_type' => 'OUT',
            'quantity' => (int) $validated['quantity_requested'],
            'transaction_date' => now(),
            'notes' => 'Auto-dispensed via request #'.$medicineRequest->id,
        ]);

        notify()->success('Request created and dispensed.');
        return redirect()->route('admin.medicine-requests.index');
    }

    // Approve/reject endpoints are no longer needed with auto-approval.
}


