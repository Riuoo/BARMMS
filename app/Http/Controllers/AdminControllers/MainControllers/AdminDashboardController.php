<?php

namespace App\Http\Controllers\AdminControllers\MainControllers;

use App\Models\AccountRequest;
use App\Models\AccomplishedProject;
use App\Models\BarangayProfile;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\Residents;
use App\Models\HealthCenterActivity;
use App\Services\PythonAnalyticsService;
use Illuminate\Support\Facades\Session;

class AdminDashboardController
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService = null)
    {
        $this->pythonService = $pythonService ?? app(PythonAnalyticsService::class);
    }

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
        $upcomingHealthActivities = HealthCenterActivity::where('activity_date', '>=', now())->count();
        
        // Get data for Python analytics
        $residents = Residents::all()->map(function($r) {
            return [
                'id' => $r->id,
                'age' => $r->age,
                'created_at' => $r->created_at ? $r->created_at->toIso8601String() : null,
            ];
        })->toArray();

        $documentRequests = DocumentRequest::all()->map(function($r) {
            return [
                'id' => $r->id,
                'document_type' => $r->document_type,
            ];
        })->toArray();

        // Get Python analytics
        $analytics = $this->pythonService->analyzeDashboard([
            'residents' => $residents,
            'document_requests' => $documentRequests,
        ]);

        return view('admin.main.dashboard', [
            'barangay_profile' => $barangay_profile,
            'totalResidents' => $totalResidents,
            'totalAccountRequests' => $totalAccountRequests,
            'totalBlotterReports' => $totalBlotterReports,
            'totalDocumentRequests' => $totalDocumentRequests,
            'totalAccomplishedProjects' => $totalAccomplishedProjects,
            'upcomingHealthActivities' => $upcomingHealthActivities,
            'residentDemographics' => $analytics['residentDemographics'],
            'residentTrends' => $analytics['residentTrends'],
            'documentRequestTypes' => $analytics['documentRequestTypes']
        ]);
    }
}
