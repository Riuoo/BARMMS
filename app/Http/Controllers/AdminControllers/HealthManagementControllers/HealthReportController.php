<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\MedicalRecord;
use App\Models\HealthCenterActivity;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\Residents;
use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HealthReportController
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService = null)
    {
        $this->pythonService = $pythonService ?? app(PythonAnalyticsService::class);
    }

    public function healthReport()
    {
        // Get data for Python analytics
        $residents = Residents::all()->map(function($r) {
            return ['id' => $r->id, 'is_pwd' => $r->is_pwd];
        })->toArray();

        $medicalRecords = MedicalRecord::all()->map(function($r) {
            return [
                'id' => $r->id,
                'consultation_datetime' => $r->consultation_datetime ? $r->consultation_datetime->toIso8601String() : null,
                'created_at' => $r->created_at ? $r->created_at->toIso8601String() : null,
            ];
        })->toArray();

        $healthActivities = HealthCenterActivity::all()->map(function($a) {
            return ['id' => $a->id];
        })->toArray();

        $medicineRequests = MedicineRequest::with('medicine')->get()->map(function($r) {
            return [
                'id' => $r->id,
                'medicine_id' => $r->medicine_id,
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

        $medicines = \App\Models\Medicine::all()->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'current_stock' => $m->current_stock,
                'minimum_stock' => $m->minimum_stock,
            ];
        })->toArray();

        $medicineBatches = \App\Models\MedicineBatch::with('medicine')->get()->map(function($b) {
            return [
                'id' => $b->id,
                'medicine_id' => $b->medicine_id,
                'expiry_date' => $b->expiry_date ? $b->expiry_date->toIso8601String() : null,
                'remaining_quantity' => $b->remaining_quantity,
            ];
        })->toArray();

        // Get Python analytics
        $analytics = $this->pythonService->analyzeHealthReport([
            'residents' => $residents,
            'medical_records' => $medicalRecords,
            'health_activities' => $healthActivities,
            'medicine_requests' => $medicineRequests,
            'medicine_transactions' => $medicineTransactions,
            'medicines' => $medicines,
            'medicine_batches' => $medicineBatches,
        ]);

        // Get recent activities (still from PHP - display data)
        $recentConsultations = MedicalRecord::with('resident')
            ->orderBy('consultation_datetime', 'desc')
            ->limit(5)
            ->get();

        $upcomingActivities = HealthCenterActivity::upcoming()
            ->orderBy('activity_date', 'asc')
            ->limit(5)
            ->get();

        // Medicine stats (still from PHP - inventory data)
        $lowStockMedicines = \App\Models\Medicine::whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderByRaw('(minimum_stock - current_stock) DESC')
            ->limit(5)
            ->get();

        $expiringBatches = \App\Models\MedicineBatch::with('medicine')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->limit(5)
            ->get();

        $medicineStats = [
            'low_stock' => $lowStockMedicines->count(),
            'expiring_soon' => $expiringBatches->count(),
            'low_stock_details' => $lowStockMedicines,
            'expiring_details' => $expiringBatches,
        ];

        // Static data
        $analyticsAlerts = [
            '3 children in Zone 2 are at high risk for malnutrition.',
            'Increase in respiratory complaints this month.',
        ];

        $kmeansResults = [
            ['description' => 'Cluster 1: High-risk elderly in Purok 3'],
            ['description' => 'Cluster 2: Children with incomplete vaccinations in Zone 1'],
        ];

        return view('admin.health.health-reports', [
            'totalResidents' => $analytics['totalResidents'],
            'totalConsultations' => $analytics['totalConsultations'],
            'totalActivities' => $analytics['totalActivities'],
            'recentConsultations' => $recentConsultations,
            'upcomingActivities' => $upcomingActivities,
            'pwdDistribution' => $analytics['pwdDistribution'],
            'monthlyConsultations' => $analytics['monthlyConsultations'],
            'analyticsAlerts' => $analyticsAlerts,
            'kmeansResults' => $kmeansResults,
            'bhwStats' => $analytics['bhwStats'],
            'medicineStats' => $medicineStats,
            'topRequestedMedicines' => $analytics['topRequestedMedicines'],
            'topDispensedMedicines' => $analytics['topDispensedMedicines'],
        ]);
    }

    public function generateComprehensiveReport(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        // Medical Consultations Summary
        $consultations = MedicalRecord::whereBetween('consultation_datetime', [$startDate, $endDate])->get();
        $consultationSummary = [
            'total' => $consultations->count(),
            'by_type' => $consultations->groupBy('consultation_type')->map->count(),
            'by_status' => $consultations->groupBy('status')->map->count(),
            'common_complaints' => $consultations->groupBy('complaint')->map->count()->sortDesc()->take(10),
        ];

        // Health Center Activities Summary
        $activities = HealthCenterActivity::whereBetween('activity_date', [$startDate, $endDate])->get();
        $activitySummary = [
            'total' => $activities->count(),
            'by_type' => $activities->groupBy('activity_type')->map->count(),
            'by_status' => $activities->groupBy('status')->map->count(),
            'total_budget' => $activities->sum('budget'),
            'total_participants' => $activities->sum('actual_participants'),
        ];

        return view('admin.health.comprehensive', compact(
            'startDate',
            'endDate',
            'consultationSummary',
            'activitySummary'
        ));
    }

    public function exportReport(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        // Generate CSV or PDF report
        $reportType = $request->get('format', 'csv');
        
        if ($reportType === 'pdf') {
            return $this->generatePDFReport($startDate, $endDate);
        } else {
            return $this->generateCSVReport($startDate, $endDate);
        }
    }

    private function generatePDFReport($startDate, $endDate)
    {
        // Implementation for PDF generation
        // This would use a library like DomPDF
        return response()->json(['message' => 'PDF generation not implemented yet']);
    }

    private function generateCSVReport($startDate, $endDate)
    {
        // Implementation for CSV generation
        return response()->json(['message' => 'CSV generation not implemented yet']);
    }
}
