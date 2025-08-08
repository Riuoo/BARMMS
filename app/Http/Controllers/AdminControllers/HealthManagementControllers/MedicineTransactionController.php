<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Medicine;
use App\Models\MedicineTransaction;
use Illuminate\Http\Request;

class MedicineTransactionController
{
    public function index(Request $request)
    {
        $query = MedicineTransaction::with(['medicine', 'resident']);

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->get('type'));
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('medicine', function ($mq) use ($search) {
                    $mq->where('name', 'like', "%{$search}%");
                })->orWhereHas('resident', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20)->withQueryString();

        // Stats within the same date range
        $statsBase = MedicineTransaction::query();
        if ($startDate && $endDate) {
            $statsBase->whereBetween('transaction_date', [$startDate, $endDate]);
        }
        $stats = [
            'in' => (clone $statsBase)->where('transaction_type', 'IN')->sum('quantity'),
            'out' => (clone $statsBase)->where('transaction_type', 'OUT')->sum('quantity'),
            'adjustment' => (clone $statsBase)->where('transaction_type', 'ADJUSTMENT')->sum('quantity'),
            'expired' => (clone $statsBase)->where('transaction_type', 'EXPIRED')->sum('quantity'),
        ];

        return view('admin.medicine-transactions.index', compact('transactions', 'stats'));
    }

    public function adjustStock(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $medicine->current_stock += (int) $validated['quantity'];
        $medicine->save();

        MedicineTransaction::create([
            'medicine_id' => $medicine->id,
            'transaction_type' => 'ADJUSTMENT',
            'quantity' => (int) $validated['quantity'],
            'transaction_date' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Stock adjusted.');
    }
}


