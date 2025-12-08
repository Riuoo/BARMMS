<?php

namespace App\Http\Controllers\AdminControllers\MainControllers;

use App\Models\AccountRequest;
use App\Models\AccomplishedProject;
use App\Models\BarangayProfile;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\Residents;
use App\Models\VaccinationRecord;
use App\Models\HealthCenterActivity;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AdminDashboardController
{
    public function index()
    {
        $userId = Session::get('user_id');
        $barangay_profile = $userId ? BarangayProfile::find($userId) : null;
        
        // --- Fetch Data for Dashboard Cards ---
        $totalResidents = Residents::count();
        $totalAccountRequests = AccountRequest::count();
        $totalBlotterReports = BlotterRequest::count();
        $totalDocumentRequests = DocumentRequest::count();

        // Fetch actual accomplished projects count
        $totalAccomplishedProjects = AccomplishedProject::count();
        
        // Additional metrics for enhanced dashboard
        $totalVaccinationRecords = VaccinationRecord::count();
        $upcomingHealthActivities = HealthCenterActivity::where('activity_date', '>=', now())->count();
        
        // Get resident demographics data for charts (create age brackets from age column)
        $residentDemographics = Residents::select(
            DB::raw('CASE 
                WHEN age BETWEEN 0 AND 17 THEN "0-17"
                WHEN age BETWEEN 18 AND 35 THEN "18-35"
                WHEN age BETWEEN 36 AND 50 THEN "36-50"
                WHEN age BETWEEN 51 AND 65 THEN "51-65"
                WHEN age > 65 THEN "65+"
                ELSE "Unknown"
            END as age_bracket'),
            DB::raw('count(*) as count')
        )
        ->groupBy('age_bracket')
        ->get();
            
        // Get monthly resident registration trends
        $residentTrends = Residents::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Get document request types distribution
        $documentRequestTypes = DocumentRequest::select('document_type', DB::raw('count(*) as count'))
            ->groupBy('document_type')
            ->get();

        return view('admin.main.dashboard', compact(
            'barangay_profile',
            'totalResidents',
            'totalAccountRequests',
            'totalBlotterReports',
            'totalDocumentRequests',
            'totalAccomplishedProjects',
            'totalVaccinationRecords',
            'upcomingHealthActivities',
            'residentDemographics',
            'residentTrends',
            'documentRequestTypes'
        ));
    }
}
