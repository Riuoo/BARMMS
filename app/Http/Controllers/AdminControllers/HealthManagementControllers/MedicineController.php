<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\MedicineBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicineController
{
    public function index(Request $request)
    {
        $query = Medicine::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('stock_status')) {
            $status = $request->get('stock_status');

            if ($status === 'low') {
                // Low stock: current stock is less than or equal to the defined minimum
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($status === 'sufficient') {
                // Sufficient stock: current stock is greater than the defined minimum
                $query->whereColumn('current_stock', '>', 'minimum_stock');
            }
        }

        // Only select needed columns for paginated medicines
        // Auto-deduct expired stock and log transactions once per request
        $this->deductExpiredStock();

        $medicines = $query->select([
            'id', 'name', 'generic_name', 'category', 'current_stock', 'minimum_stock', 'expiry_date', 'description', 'manufacturer', 'dosage_form'
        ])
        ->orderBy('current_stock', 'asc')  // Sort by low stock to highest stock
        ->orderBy('expiry_date', 'asc')    // Sort by shortest expiry to longest expiry
        ->paginate(10)
        ->withQueryString();

        // Detailed list for expiring-soon modal (also drives the expiring_soon stat),
        // now based on batches instead of a single medicine-level expiry date.
        $today = now()->toDateString();
        $limit = now()->addDays(30)->toDateString();

        $expiringSoonBatches = MedicineBatch::with('medicine')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $limit])
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        // Use caching for aggregate stats where safe (5 min)
        $stats = [
            'total_medicines' => \Illuminate\Support\Facades\Cache::remember('total_medicines', 300, function () {
                return Medicine::count();
            }),
            'low_stock' => \Illuminate\Support\Facades\Cache::remember('low_stock_medicines', 300, function () {
                return Medicine::whereColumn('current_stock', '<=', 'minimum_stock')->count();
            }),
            // Keep expiring_soon in sync with the modal by deriving from the same batch collection (no cache)
            'expiring_soon' => $expiringSoonBatches->count(),
        ];

        // Only select needed columns for top requested/dispensed
        $topRequested = MedicineRequest::select('medicine_id')
            ->selectRaw('COUNT(*) as requests')
            ->whereBetween('request_date', [now()->subDays(30)->toDateString(), now()->toDateString()])
            ->groupBy('medicine_id')
            ->orderByDesc('requests')
            ->with(['medicine:id,name'])
            ->limit(5)
            ->get();
        $topDispensed = MedicineTransaction::select('medicine_id')
            ->selectRaw('SUM(quantity) as total_qty')
            ->where('transaction_type', 'OUT')
            ->whereBetween('transaction_date', [now()->subDays(30), now()])
            ->groupBy('medicine_id')
            ->orderByDesc('total_qty')
            ->with(['medicine:id,name'])
            ->limit(5)
            ->get();

        return view('admin.medicines.index', compact('medicines', 'stats', 'topRequested', 'topDispensed', 'expiringSoonBatches'));
    }

    public function create()
    {
        return view('admin.medicines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:50',
            'category_other' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'dosage_form' => 'required|string|max:100',
            'manufacturer' => 'required|string|max:255',
            'current_stock' => 'sometimes|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'expiry_date' => 'required|date',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle "Other" category
        if ($validated['category'] === 'Other') {
            if (empty($request->input('category_other'))) {
                notify()->error('Please specify the category when selecting "Other".');
                return back()->withInput();
            }
            $validated['category'] = $request->input('category_other');
        }

        // Remove the category_other field as it's not needed in the database
        unset($validated['category_other']);

        $medicine = Medicine::create($validated);

        // Create initial batch if there is initial stock
        if (($medicine->current_stock ?? 0) > 0) {
            MedicineBatch::create([
                'medicine_id' => $medicine->id,
                'batch_code' => null,
                'quantity' => (int) $medicine->current_stock,
                'remaining_quantity' => (int) $medicine->current_stock,
                'expiry_date' => $medicine->expiry_date,
                'notes' => 'Initial stock on creation',
            ]);
        }

        // Log initial stock as an IN transaction so it appears in transactions/report
        if (($medicine->current_stock ?? 0) > 0) {
            MedicineTransaction::create([
                'medicine_id' => $medicine->id,
                'transaction_type' => 'IN',
                'quantity' => (int) $medicine->current_stock,
                'transaction_date' => now(),
                'notes' => 'Initial stock on creation (expiry: ' . ($medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : 'N/A') . ')',
            ]);
        }

        notify()->success('Medicine added successfully.');
        return redirect()->route('admin.medicines.index');
    }

    public function edit(Medicine $medicine)
    {
        $batches = $medicine->batches()
            ->orderBy('expiry_date')
            ->orderBy('created_at')
            ->get();

        return view('admin.medicines.edit', compact('medicine', 'batches'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:50',
            'category_other' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'dosage_form' => 'required|string|max:100',
            'manufacturer' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle "Other" category
        if ($validated['category'] === 'Other') {
            if (empty($request->input('category_other'))) {
                notify()->error('Please specify the category when selecting "Other".');
                return back()->withInput();
            }
            $validated['category'] = $request->input('category_other');
        }

        // Remove the category_other field as it's not needed in the database
        unset($validated['category_other']);

        $medicine->update($validated);

        notify()->success('Medicine updated successfully.');
        return redirect()->route('admin.medicines.index');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        notify()->success('Medicine deleted.');
        return redirect()->route('admin.medicines.index');
    }

    public function restock(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'restock_expiry_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $medicine->current_stock += (int) $validated['quantity'];
        // Do not overwrite existing medicine expiry_date; track expiry per batch instead.
        // (You may later choose to derive a summary expiry from batches if needed.)
        $medicine->save();

        // Create a new batch for this restock
        $batch = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_code' => null,
            'quantity' => (int) $validated['quantity'],
            'remaining_quantity' => (int) $validated['quantity'],
            'expiry_date' => $validated['restock_expiry_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        MedicineTransaction::create([
            'medicine_id' => $medicine->id,
            'transaction_type' => 'IN',
            'quantity' => (int) $validated['quantity'],
            'transaction_date' => now(),
            'notes' => ($validated['notes'] ?? 'Restock') . ' (batch ID: ' . $batch->id . ', expiry: ' . $validated['restock_expiry_date'] . ')',
        ]);

        notify()->success('Stock updated.');
        return back();
    }

    /**
     * Deduct expired stock and log as EXPIRED transactions.
     */
    private function deductExpiredStock(): void
    {
        $today = now()->toDateString();
        // Handle expiry per batch rather than per-medicine.
        $expiredBatches = MedicineBatch::whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->where('remaining_quantity', '>', 0)
            ->get();

        foreach ($expiredBatches as $batch) {
            $qty = (int) $batch->remaining_quantity;
            if ($qty <= 0) {
                continue;
            }

            // Zero out the batch and adjust the parent medicine stock
            $batch->remaining_quantity = 0;
            $batch->save();

            Medicine::where('id', $batch->medicine_id)->decrement('current_stock', $qty);

            MedicineTransaction::create([
                'medicine_id' => $batch->medicine_id,
                'transaction_type' => 'EXPIRED',
                'quantity' => $qty,
                'transaction_date' => now(),
                'notes' => 'Auto-deducted expired stock from batch ID ' . $batch->id . ' (expiry: ' . ($batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : 'N/A') . ')',
            ]);
        }
    }

    public function report(Request $request)
    {
        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        $transactions = MedicineTransaction::with('medicine')
            ->whereBetween('transaction_date', [$start, $end])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Consolidated medicine request analytics - single query for all request data
        $requestAnalytics = $this->getConsolidatedRequestAnalytics($start, $end);

        $topDispensed = MedicineTransaction::select('medicine_id')
            ->selectRaw('SUM(quantity) as total_qty')
            ->where('transaction_type', 'OUT')
            ->whereBetween('transaction_date', [$start, $end])
            ->groupBy('medicine_id')
            ->orderByDesc('total_qty')
            ->with('medicine')
            ->limit(10)
            ->get();

        // Category distribution
        $categoryCounts = Medicine::select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        // Monthly dispense trend (last 6 months)
        $trendStart = $start->copy()->subMonths(5)->startOfMonth();
        $monthlyDispensed = MedicineTransaction::where('transaction_type', 'OUT')
            ->whereBetween('transaction_date', [$trendStart, $end])
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(quantity) as qty')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Requests by age bracket
        $requestsByAgeBracket = DB::table('medicine_requests as mr')
            ->join('residents as r', 'r.id', '=', 'mr.resident_id')
            ->whereBetween('mr.request_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('CASE 
                WHEN r.age BETWEEN 0 AND 12 THEN "0-12"
                WHEN r.age BETWEEN 13 AND 17 THEN "13-17"
                WHEN r.age BETWEEN 18 AND 35 THEN "18-35"
                WHEN r.age BETWEEN 36 AND 60 THEN "36-60"
                ELSE "61+" END as bracket, COUNT(*) as count')
            ->groupBy('bracket')
            ->orderBy('bracket')
            ->get();

        // Top requested people by purok (unique residents who made at least one request)
        $topRequestedPeopleByPurok = DB::table('medicine_requests as mr')
            ->join('residents as r', 'r.id', '=', 'mr.resident_id')
            ->whereBetween('mr.request_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('CASE 
                WHEN r.address LIKE "%Purok 1%" THEN "Purok 1"
                WHEN r.address LIKE "%Purok 2%" THEN "Purok 2"
                WHEN r.address LIKE "%Purok 3%" THEN "Purok 3"
                WHEN r.address LIKE "%Purok 4%" THEN "Purok 4"
                WHEN r.address LIKE "%Purok 5%" THEN "Purok 5"
                WHEN r.address LIKE "%Purok 6%" THEN "Purok 6"
                WHEN r.address LIKE "%Purok 7%" THEN "Purok 7"
                WHEN r.address LIKE "%Purok 8%" THEN "Purok 8"
                WHEN r.address LIKE "%Purok 9%" THEN "Purok 9"
                ELSE "Other" 
            END as purok, COUNT(DISTINCT mr.resident_id) as people')
            ->groupBy('purok')
            ->orderBy('purok')
                    ->get();

        return view('admin.medicines.report', compact(
            'transactions', 'requestAnalytics', 'topDispensed', 'start', 'end',
            'categoryCounts', 'monthlyDispensed', 'requestsByAgeBracket',
            'topRequestedPeopleByPurok'
        ));
    }

    /**
     * Get consolidated medicine request analytics from a single query
     * Eliminates redundancy by generating all request views from one data source
     */
    private function getConsolidatedRequestAnalytics($start, $end)
    {
        // Get medicine requests with purok information for geographic analysis
        $requestsByPurok = DB::table('medicine_requests as mr')
            ->join('residents as r', 'r.id', '=', 'mr.resident_id')
            ->join('medicines as m', 'm.id', '=', 'mr.medicine_id')
            ->whereBetween('mr.request_date', [$start->toDateString(), $end->toDateString()])
            ->select([
                'm.name as medicine_name',
                'm.category as medicine_category',
                DB::raw('CASE 
                    WHEN r.address LIKE "%Purok 1%" THEN "Purok 1"
                    WHEN r.address LIKE "%Purok 2%" THEN "Purok 2"
                    WHEN r.address LIKE "%Purok 3%" THEN "Purok 3"
                    WHEN r.address LIKE "%Purok 4%" THEN "Purok 4"
                    WHEN r.address LIKE "%Purok 5%" THEN "Purok 5"
                    WHEN r.address LIKE "%Purok 6%" THEN "Purok 6"
                    WHEN r.address LIKE "%Purok 7%" THEN "Purok 7"
                    WHEN r.address LIKE "%Purok 8%" THEN "Purok 8"
                    WHEN r.address LIKE "%Purok 9%" THEN "Purok 9"
                    ELSE "Other" 
                END as purok'),
                DB::raw('COUNT(*) as requests')
            ])
            ->groupBy('m.name', 'm.category', DB::raw('CASE 
                WHEN r.address LIKE "%Purok 1%" THEN "Purok 1"
                WHEN r.address LIKE "%Purok 2%" THEN "Purok 2"
                WHEN r.address LIKE "%Purok 3%" THEN "Purok 3"
                WHEN r.address LIKE "%Purok 4%" THEN "Purok 4"
                WHEN r.address LIKE "%Purok 5%" THEN "Purok 5"
                WHEN r.address LIKE "%Purok 6%" THEN "Purok 6"
                WHEN r.address LIKE "%Purok 7%" THEN "Purok 7"
                WHEN r.address LIKE "%Purok 8%" THEN "Purok 8"
                WHEN r.address LIKE "%Purok 9%" THEN "Purok 9"
                ELSE "Other" 
            END'))
            ->orderBy('purok')
            ->orderByDesc('requests')
            ->get();

        // Get overall top requested medicines
        $overallTopRequested = DB::table('medicine_requests as mr')
            ->join('medicines as m', 'm.id', '=', 'mr.medicine_id')
            ->whereBetween('mr.request_date', [$start->toDateString(), $end->toDateString()])
            ->select([
                'm.name as medicine_name',
                'm.category as medicine_category',
                DB::raw('COUNT(*) as requests')
            ])
            ->groupBy('m.name', 'm.category')
            ->orderByDesc('requests')
            ->limit(10)
            ->get();

        // Note: Debug logging removed for production

        // Group by purok for geographic analysis
        $byPurok = $requestsByPurok->groupBy('purok')
            ->map(function ($purokGroup) {
                return $purokGroup->take(5)->map(function ($item) {
                    return [
                        'medicine_name' => $item->medicine_name,
                        'requests' => $item->requests
                    ];
                })->values();
            });

        // Overall top requested medicines
        $overall = $overallTopRequested->map(function ($item) {
            return [
                'medicine' => ['name' => $item->medicine_name],
                'requests' => $item->requests
            ];
        })->values();

        return [
            'by_purok' => $byPurok,
            'overall' => $overall
        ];
    }
}


