<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\CommunityComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResidentRequestListController
{
    public function myRequests(Request $request)
    {
        $userId = Session::get('user_id');

        // Get all requests without pagination first
        $documentQuery = DocumentRequest::where('resident_id', $userId);
        $blotterQuery = BlotterRequest::where('resident_id', $userId);
        $complaintQuery = CommunityComplaint::where('resident_id', $userId);

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            
            $documentQuery->where(function ($q) use ($search) {
                $q->where('document_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            
            $blotterQuery->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            
            $complaintQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if ($request->filled('status')) {
            $status = $request->get('status');
            $documentQuery->where('status', $status);
            $blotterQuery->where('status', $status);
            $complaintQuery->where('status', $status);
        }

        // Get all results
        $allDocumentRequests = $documentQuery
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'completed')")
            ->orderByDesc('created_at')
            ->get();

        $allBlotterRequests = $blotterQuery->orderByDesc('created_at')->get();
        $allCommunityComplaints = $complaintQuery->orderByDesc('created_at')->get();

        // Combine all requests into a single collection
        $allRequests = collect();

        // Add document requests
        foreach ($allDocumentRequests as $docRequest) {
            $allRequests->push((object) [
                'id' => $docRequest->id,
                'type' => 'document',
                'request' => $docRequest,
                'created_at' => $docRequest->created_at,
                'status' => $docRequest->status,
            ]);
        }

        // Add blotter requests
        foreach ($allBlotterRequests as $blotterRequest) {
            $allRequests->push((object) [
                'id' => $blotterRequest->id,
                'type' => 'blotter',
                'request' => $blotterRequest,
                'created_at' => $blotterRequest->created_at,
                'status' => $blotterRequest->status,
            ]);
        }

        // Add community complaints
        foreach ($allCommunityComplaints as $complaint) {
            $allRequests->push((object) [
                'id' => $complaint->id,
                'type' => 'complaint',
                'request' => $complaint,
                'created_at' => $complaint->created_at,
                'status' => $complaint->status,
            ]);
        }

        // Sort by creation date (latest first)
        $allRequests = $allRequests->sortByDesc('created_at');

        // Create unified pagination
        $perPage = 15; // Show 15 requests per page
        $currentPage = (int) ($request->get('page', 1));
        $offset = ($currentPage - 1) * $perPage;
        $paginatedSlice = $allRequests->slice($offset, $perPage)->values();

        // Create a LengthAwarePaginator instance
        $paginatedRequests = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedSlice,
            $allRequests->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        // Separate the paginated requests back into their types for the view
        $documentRequests = collect();
        $blotterRequests = collect();
        $communityComplaints = collect();

        foreach ($paginatedRequests as $item) {
            switch ($item->type) {
                case 'document':
                    $documentRequests->push($item->request);
                    break;
                case 'blotter':
                    $blotterRequests->push($item->request);
                    break;
                case 'complaint':
                    $communityComplaints->push($item->request);
                    break;
            }
        }

        // Compute resident notifications (mirror admin style but for this user only)
        $residentNotifications = collect();
        foreach ($allDocumentRequests as $docReq) {
            if ($docReq->status === 'approved') {
                $residentNotifications->push((object) [
                    'id' => $docReq->id,
                    'type' => 'document_request',
                    'message' => 'Your ' . $docReq->document_type . ' is ready for pickup.',
                    'created_at' => $docReq->updated_at,
                    'is_read' => (bool) ($docReq->resident_is_read ?? true),
                    'link' => route('resident.my-requests')
                ]);
            }
        }

        return view('resident.my_requests', compact(
            'documentRequests',
            'blotterRequests',
            'communityComplaints',
            'residentNotifications',
            'paginatedRequests'
        ));
    }
} 