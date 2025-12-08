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

        // Fetch comprehensive data for dashboard statistics
        // Query blotters by complainant_name since residents see blotters they filed
        $totalBlotterRequests = BlotterRequest::where('complainant_name', $resident->full_name)->count();
        $totalDocumentRequests = DocumentRequest::where('resident_id', $userId)->count();
        $totalCommunityConcerns = CommunityConcern::where('resident_id', $userId)->count();
        
        // Get status-based counts using more efficient queries
        $pendingCounts = $this->getStatusCounts($userId, $resident->full_name, 'pending');
        $completedCounts = $this->getStatusCounts($userId, $resident->full_name, 'completed');
        
        // Fetch recent items for dashboard
        $recentBlotterRequests = BlotterRequest::where('complainant_name', $resident->full_name)->orderBy('created_at', 'desc')->take(1)->get();
        $recentDocumentRequests = DocumentRequest::where('resident_id', $userId)->orderBy('created_at', 'desc')->take(1)->get();
        $recentCommunityConcerns = CommunityConcern::where('resident_id', $userId)->orderBy('created_at', 'desc')->take(1)->get();

        return view('resident.dashboard', compact(
            'resident',
            'totalBlotterRequests',
            'totalDocumentRequests', 
            'totalCommunityConcerns',
            'pendingCounts',
            'completedCounts',
            'recentBlotterRequests',
            'recentDocumentRequests',
            'recentCommunityConcerns'
        ));
    }

    /**
     * Get counts for a specific status across all request types
     */
    private function getStatusCounts($userId, $residentName, $status)
    {
        return [
            'blotter' => BlotterRequest::where('complainant_name', $residentName)->where('status', $status)->count(),
            'document' => DocumentRequest::where('resident_id', $userId)->where('status', $status)->count(),
            'concern' => CommunityConcern::where('resident_id', $userId)->where('status', $status)->count(),
        ];
    }
} 