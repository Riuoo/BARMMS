<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\VaccinationRecord;
use App\Models\MedicalRecord;
use App\Models\HealthCenterActivity;
use App\Models\MedicineRequest;
use App\Models\MedicineTransaction;
use App\Models\Residents;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HealthReportController
{
    public function healthReport()
    {
        // Get summary statistics
        $totalResidents = Residents::count();
        $totalVaccinations = VaccinationRecord::count();
        $totalConsultations = MedicalRecord::count();
        $totalActivities = HealthCenterActivity::count();

        // Get recent activities
        $recentConsultations = MedicalRecord::with('resident')
            ->orderBy('consultation_datetime', 'desc')
            ->limit(5)
            ->get();

        $upcomingActivities = HealthCenterActivity::upcoming()
            ->orderBy('activity_date', 'asc')
            ->limit(5)
            ->get();

        $dueVaccinations = VaccinationRecord::with('resident')
            ->whereNotNull('next_dose_date')
            ->where('next_dose_date', '<=', now()->addDays(30))
            ->orderBy('next_dose_date', 'asc')
            ->limit(10)
            ->get();

        // PWD distribution
        $pwdDistribution = Residents::selectRaw('is_pwd, count(*) as count')
            ->groupBy('is_pwd')
            ->get();

        // Monthly consultation trends
        $monthlyConsultations = MedicalRecord::selectRaw('DATE_FORMAT(consultation_datetime, "%Y-%m") as month, count(*) as count')
            ->where('consultation_datetime', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $overdueVaccinations = VaccinationRecord::with('resident')
            ->whereNotNull('next_dose_date')
            ->where('next_dose_date', '<', now())
            ->orderBy('next_dose_date', 'asc')
            ->limit(10)
            ->get();

        $analyticsAlerts = [
            '3 children in Zone 2 are at high risk for malnutrition.',
            'Increase in respiratory complaints this month.',
        ];

        $kmeansResults = [
            ['description' => 'Cluster 1: High-risk elderly in Purok 3'],
            ['description' => 'Cluster 2: Children with incomplete vaccinations in Zone 1'],
        ];

        $decisionTreeResults = [
            ['description' => 'Children with incomplete vaccinations are at higher risk for measles.'],
            ['description' => 'Elderly with hypertension and diabetes are at higher risk for complications.'],
        ];

        $bhwStats = [
            'consultations' => MedicalRecord::whereMonth('created_at', now()->month)->count(),
            'vaccinations' => VaccinationRecord::whereMonth('created_at', now()->month)->count(),
        ];

        // Medicine analytics (30-day window) - Detailed inventory with priority sorting
        $lowStockMedicines = \App\Models\Medicine::whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderByRaw('(minimum_stock - current_stock) DESC') // Most critical first
            ->limit(5)
            ->get();

        // Expiring soon based on batches rather than a single medicine expiry date
        $expiringBatches = \App\Models\MedicineBatch::with('medicine')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc') // Soonest to expire first
            ->limit(5)
            ->get();

        $medicineStats = [
            'low_stock' => $lowStockMedicines->count(),
            'expiring_soon' => $expiringBatches->count(),
            'low_stock_details' => $lowStockMedicines,
            'expiring_details' => $expiringBatches,
        ];

        $topRequestedMedicines = MedicineRequest::select('medicine_id')
            ->selectRaw('COUNT(*) as requests')
            ->whereBetween('request_date', [now()->subDays(30)->toDateString(), now()->toDateString()])
            ->groupBy('medicine_id')
            ->orderByDesc('requests')
            ->with('medicine')
            ->limit(5)
            ->get();

        $topDispensedMedicines = MedicineTransaction::select('medicine_id')
            ->selectRaw('SUM(quantity) as total_qty')
            ->where('transaction_type', 'OUT')
            ->whereBetween('transaction_date', [now()->subDays(30), now()])
            ->groupBy('medicine_id')
            ->orderByDesc('total_qty')
            ->with('medicine')
            ->limit(5)
            ->get();

        return view('admin.health.health-reports', compact(
            'totalResidents',
            'totalVaccinations',
            'totalConsultations',
            'totalActivities',
            'recentConsultations',
            'upcomingActivities',
            'dueVaccinations',
            'pwdDistribution',
            'monthlyConsultations',
            'overdueVaccinations',
            'analyticsAlerts',
            'kmeansResults',
            'decisionTreeResults',
            'bhwStats',
            'medicineStats',
            'topRequestedMedicines',
            'topDispensedMedicines'
        ));
    }

    public function generateComprehensiveReport(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        // Vaccination Summary
        $vaccinations = VaccinationRecord::whereBetween('vaccination_date', [$startDate, $endDate])->get();
        $vaccinationSummary = [
            'total' => $vaccinations->count(),
            'by_type' => $vaccinations->groupBy('vaccine_type')->map->count(),
            'by_month' => $vaccinations->groupBy(function($record) {
                return $record->vaccination_date->format('Y-m');
            })->map->count(),
        ];

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
            'vaccinationSummary',
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
