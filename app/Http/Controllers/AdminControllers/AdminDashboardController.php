<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\AccountRequest;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\Residents;

class AdminDashboardController
{
    public function index()
    {
        // --- Fetch Data for Dashboard Cards ---
        $totalResidents = Residents::count();
        $totalAccountRequests = AccountRequest::count();
        $totalBlotterReports = BlotterRequest::count();
        $totalDocumentRequests = DocumentRequest::count();

        // Dummy values for now, replace with actual queries when models exist
        $totalAccomplishedProjects = 15; // Replace with actual query
        $totalHealthReports = 10; // Replace with actual query

        return view('admin.dashboard', compact(
            'totalResidents',
            'totalAccountRequests',
            'totalBlotterReports',
            'totalDocumentRequests',
            'totalAccomplishedProjects',
            'totalHealthReports'
        ));
    }
}
