<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\CommunityConcern;
use App\Models\Residents;
use Illuminate\Support\Facades\Session;

class ResidentDashboardController
{
    public function dashboard()
    {
        $userId = Session::get('user_id');
        $resident = Residents::find($userId);

        if (!$resident) {
            return redirect()->route('landing');
        }

        // Fetch the resident's blotter, document requests, and community concerns for dashboard statistics
        $blotterRequests = BlotterRequest::where('resident_id', $userId)->orderBy('created_at', 'desc')->get();
        $documentRequests = DocumentRequest::where('resident_id', $userId)->orderBy('created_at', 'desc')->get();
        $communityConcerns = CommunityConcern::where('resident_id', $userId)->orderBy('created_at', 'desc')->get();

        return view('resident.dashboard', compact('resident', 'blotterRequests', 'documentRequests', 'communityConcerns'));
    }
} 