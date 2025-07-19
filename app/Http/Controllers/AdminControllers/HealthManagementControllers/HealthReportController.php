<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\PatientRecord;
use App\Models\VaccinationRecord;
use App\Models\MedicalLogbook;
use App\Models\HealthCenterActivity;
use App\Models\Residents;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HealthReportController
{
    public function healthReport()
    {
        // Get summary statistics
        $totalResidents = Residents::count();
        $totalPatientRecords = PatientRecord::count();
        $totalVaccinations = VaccinationRecord::count();
        $totalConsultations = MedicalLogbook::count();
        $totalActivities = HealthCenterActivity::count();

        // Get recent activities
        $recentConsultations = MedicalLogbook::with('resident')
            ->orderBy('consultation_date', 'desc')
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

        // Health status distribution
        $healthStatusDistribution = Residents::selectRaw('health_status, count(*) as count')
            ->whereNotNull('health_status')
            ->groupBy('health_status')
            ->get();

        // Risk level distribution
        $riskLevelDistribution = PatientRecord::selectRaw('risk_level, count(*) as count')
            ->groupBy('risk_level')
            ->get();

        // Monthly consultation trends
        $monthlyConsultations = MedicalLogbook::selectRaw('DATE_FORMAT(consultation_date, "%Y-%m") as month, count(*) as count')
            ->where('consultation_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.health.health-reports', compact(
            'totalResidents',
            'totalPatientRecords',
            'totalVaccinations',
            'totalConsultations',
            'totalActivities',
            'recentConsultations',
            'upcomingActivities',
            'dueVaccinations',
            'healthStatusDistribution',
            'riskLevelDistribution',
            'monthlyConsultations'
        ));
    }

    public function generateComprehensiveReport(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        // Patient Records Summary
        $patientRecords = PatientRecord::whereBetween('created_at', [$startDate, $endDate])->get();
        $patientSummary = [
            'total' => $patientRecords->count(),
            'by_risk_level' => $patientRecords->groupBy('risk_level')->map->count(),
            'by_blood_type' => $patientRecords->whereNotNull('blood_type')->groupBy('blood_type')->map->count(),
        ];

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
        $consultations = MedicalLogbook::whereBetween('consultation_date', [$startDate, $endDate])->get();
        $consultationSummary = [
            'total' => $consultations->count(),
            'by_type' => $consultations->groupBy('consultation_type')->map->count(),
            'by_status' => $consultations->groupBy('status')->map->count(),
            'common_complaints' => $consultations->groupBy('chief_complaint')->map->count()->sortDesc()->take(10),
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

        return view('admin.health-reports.comprehensive', compact(
            'startDate',
            'endDate',
            'patientSummary',
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
