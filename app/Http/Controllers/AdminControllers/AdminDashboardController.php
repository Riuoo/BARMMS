<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\AccountRequest;
use App\Models\AccomplishedProject;
use App\Models\BarangayProfile;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\Residents;
use Illuminate\Support\Facades\Session;

class AdminDashboardController
{
    public function index()
    {
        $userId = Session::get('user_id');
        $barangay_profile = BarangayProfile::find($userId);
        
        // --- Fetch Data for Dashboard Cards ---
        $totalResidents = Residents::count();
        $totalAccountRequests = AccountRequest::count();
        $totalBlotterReports = BlotterRequest::count();
        $totalDocumentRequests = DocumentRequest::count();

        // Fetch actual accomplished projects count
        $totalAccomplishedProjects = AccomplishedProject::count();
        $totalHealthReports = 10; // Replace with actual query when health reports model exists

        return view('admin.modals.dashboard', compact(
            'barangay_profile',
            'totalResidents',
            'totalAccountRequests',
            'totalBlotterReports',
            'totalDocumentRequests',
            'totalAccomplishedProjects',
            'totalHealthReports'
        ));
    }
}
