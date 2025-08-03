<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\HealthStatus;
use App\Models\CommunityComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResidentRequestListController
{
    public function myRequests(Request $request)
    {
        $userId = Session::get('user_id');

        // --- Document Requests ---
        $documentQuery = DocumentRequest::where('user_id', $userId);
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
        $blotterQuery = BlotterRequest::where('user_id', $userId);
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

        // --- Health Status Requests ---
        $healthQuery = HealthStatus::where('user_id', $userId);
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $healthQuery->where(function ($q) use ($search) {
                $q->where('concern_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $healthQuery->where('status', $request->get('status'));
        }
        $healthStatusRequests = $healthQuery->orderByDesc('created_at')->get();

        // --- Community Complaints ---
        $complaintQuery = CommunityComplaint::where('user_id', $userId);
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

        return view('resident.my_requests', compact(
            'documentRequests',
            'blotterRequests',
            'healthStatusRequests',
            'communityComplaints'
        ));
    }
} 