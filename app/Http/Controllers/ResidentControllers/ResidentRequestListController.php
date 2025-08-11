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

        // --- Document Requests ---
        $documentQuery = DocumentRequest::where('resident_id', $userId);
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $documentQuery->where(function ($q) use ($search) {
                $q->where('document_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $documentQuery->where('status', $request->get('status'));
        }
        $documentRequests = $documentQuery
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'completed')")
            ->orderByDesc('created_at')
            ->get();

        // --- Blotter Requests ---
        $blotterQuery = BlotterRequest::where('resident_id', $userId);
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $blotterQuery->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $blotterQuery->where('status', $request->get('status'));
        }
        $blotterRequests = $blotterQuery->orderByDesc('created_at')->get();

        // --- Community Complaints ---
        $complaintQuery = CommunityComplaint::where('resident_id', $userId);
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $complaintQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $complaintQuery->where('status', $request->get('status'));
        }
        $communityComplaints = $complaintQuery->orderByDesc('created_at')->get();

        // Compute resident notifications (mirror admin style but for this user only)
        $residentNotifications = collect();
        foreach ($documentRequests as $req) {
            if ($req->status === 'approved') {
                $residentNotifications->push((object) [
                    'id' => $req->id,
                    'type' => 'document_request',
                    'message' => 'Your ' . $req->document_type . ' is ready for pickup.',
                    'created_at' => $req->updated_at,
                    'is_read' => (bool) ($req->resident_is_read ?? true),
                    'link' => route('resident.my-requests')
                ]);
            }
        }

        return view('resident.my_requests', compact(
            'documentRequests',
            'blotterRequests',
            'communityComplaints',
            'residentNotifications'
        ));
    }
} 