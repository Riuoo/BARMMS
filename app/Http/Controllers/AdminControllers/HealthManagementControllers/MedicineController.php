<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\MedicineBatch;
use App\Models\Residents;
use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicineController
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService = null)
    {
        $this->pythonService = $pythonService ?? app(PythonAnalyticsService::class);
    }
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
            'total_stock_units' => \Illuminate\Support\Facades\Cache::remember('total_stock_units', 300, function () {
                return Medicine::sum('current_stock');
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

        // Get transactions for pagination (still from PHP - display data)
        $transactions = MedicineTransaction::with('medicine')
            ->whereBetween('transaction_date', [$start, $end])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get data for Python analytics
        $medicineRequests = MedicineRequest::with('medicine')->get()->map(function($r) {
            return [
                'id' => $r->id,
                'medicine_id' => $r->medicine_id,
                'resident_id' => $r->resident_id,
                'request_date' => $r->request_date ? $r->request_date->toIso8601String() : null,
            ];
        })->toArray();

        $medicineTransactions = MedicineTransaction::with('medicine')->get()->map(function($t) {
            return [
                'id' => $t->id,
                'medicine_id' => $t->medicine_id,
                'transaction_type' => $t->transaction_type,
                'quantity' => $t->quantity,
                'transaction_date' => $t->transaction_date ? $t->transaction_date->toIso8601String() : null,
            ];
        })->toArray();

        $medicines = Medicine::all()->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category,
            ];
        })->toArray();

        $residents = Residents::all()->map(function($r) {
            return [
                'id' => $r->id,
                'age' => $r->age,
                'address' => $r->address,
            ];
        })->toArray();

        // Get Python analytics
        $analytics = $this->pythonService->analyzeMedicineReport(
            [
                'medicine_requests' => $medicineRequests,
                'medicine_transactions' => $medicineTransactions,
                'medicines' => $medicines,
                'residents' => $residents,
            ],
            $start->toIso8601String(),
            $end->toIso8601String()
        );

        return view('admin.medicines.report', [
            'transactions' => $transactions,
            'requestAnalytics' => $analytics['requestAnalytics'],
            'topDispensed' => $analytics['topDispensed'],
            'start' => $start,
            'end' => $end,
            'categoryCounts' => $analytics['categoryCounts'],
            'monthlyDispensed' => $analytics['monthlyDispensed'],
            'requestsByAgeBracket' => $analytics['requestsByAgeBracket'],
            'topRequestedPeopleByPurok' => $analytics['topRequestedPeopleByPurok'],
        ]);
    }

}


