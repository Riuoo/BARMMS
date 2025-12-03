<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\DocumentRequest;
use App\Models\BlotterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ResidentNotificationController
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('landing');
        }

        // (Reverted) No auto-mark as read here

        $resident = \App\Models\Residents::find($userId);
        $complainantName = $resident ? $resident->name : null;

        $docs = DocumentRequest::where('resident_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();
        // Query blotters by complainant_name since residents see blotters they filed
        $blotters = $complainantName ? \App\Models\BlotterRequest::where('complainant_name', $complainantName)
            ->orderBy('updated_at', 'desc')
            ->get() : collect();

        $notifications = collect();
        foreach ($docs as $doc) {
            if ($doc->status === 'approved') {
                $notifications->push((object) [
                    'id' => $doc->id,
                    'type' => 'document_request',
                    'message' => 'Your ' . $doc->document_type . ' is ready for pickup.',
                    'created_at' => $doc->updated_at,
                    'is_read' => (bool) ($doc->resident_is_read ?? true),
                    'link' => route('resident.my-requests'),
                ]);
            } elseif ($doc->status === 'completed') {
                $notifications->push((object) [
                    'id' => $doc->id,
                    'type' => 'document_request',
                    'message' => 'Your ' . $doc->document_type . ' request has been completed.',
                    'created_at' => $doc->updated_at,
                    'is_read' => true, // completed items considered read for resident
                    'link' => route('resident.my-requests'),
                ]);
            }
        }
        // Add blotter notifications
        foreach ($blotters as $blotter) {
            if ($blotter->status === 'approved') {
                $notifications->push((object) [
                    'id' => $blotter->id,
                    'type' => 'blotter_request',
                    'message' => 'Your blotter report for "' . $blotter->type . '" has been approved. A hearing/summon is scheduled for ' . ($blotter->summon_date ? $blotter->summon_date->format('F d, Y h:i A') : 'N/A') . '.',
                    'created_at' => $blotter->updated_at,
                    'is_read' => (bool) ($blotter->resident_is_read ?? true),
                    'link' => route('resident.my-requests'),
                ]);
            } elseif ($blotter->status === 'completed') {
                $notifications->push((object) [
                    'id' => $blotter->id,
                    'type' => 'blotter_request',
                    'message' => 'Your blotter report for "' . $blotter->type . '" has been completed.',
                    'created_at' => $blotter->updated_at,
                    'is_read' => true,
                    'link' => route('resident.my-requests'),
                ]);
            }
        }

        // Totals across ALL notifications (not limited by pagination)
        $total_unread = $notifications->where('is_read', false)->count();
        $total_read = $notifications->where('is_read', true)->count();

        // Search filter
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $notifications = $notifications->filter(function ($n) use ($search) {
                return strpos(strtolower($n->message), $search) !== false;
            });
        }

        // Read status filter
        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $notifications = $notifications->where('is_read', false);
            } elseif ($request->read_status === 'read') {
                $notifications = $notifications->where('is_read', true);
            }
        }

        $notifications = $notifications->sortByDesc('created_at');

        // Create a custom paginator for the filtered results
        $perPage = 15; // Show 15 notifications per page
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedNotifications = $notifications->slice($offset, $perPage);
        
        // Create a LengthAwarePaginator instance
        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedNotifications,
            $notifications->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('resident.notifications', compact('notifications', 'total_unread', 'total_read'));
    }

    public function count()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['total' => 0, 'notifications' => []]);
        }
        
        // Fetch unread document notifications
        $docUnread = DocumentRequest::where('resident_id', $userId)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('resident_is_read')->orWhere('resident_is_read', false);
            })->get()->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'type' => 'document_request',
                    'title' => 'Document Ready for Pickup',
                    'message' => 'Your document request for ' . $doc->document_type . ' is ready for pickup.',
                    'created_at' => $doc->created_at,
                    'is_read' => (bool) $doc->resident_is_read
                ];
            });

        // Fetch unread blotter notifications
        $resident = \App\Models\Residents::find($userId);
        $complainantName = $resident ? $resident->name : null;
        // Query blotters by complainant_name since residents see blotters they filed
        $blotterUnread = $complainantName ? BlotterRequest::where('complainant_name', $complainantName)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('resident_is_read')->orWhere('resident_is_read', false);
            })->get()->map(function ($blotter) {
                return [
                    'id' => $blotter->id,
                    'type' => 'blotter_request',
                    'title' => 'Blotter Report Approved',
                    'message' => 'Your blotter report has been approved. A hearing is scheduled for ' . $blotter->summon_date . '.',
                    'created_at' => $blotter->created_at,
                    'is_read' => (bool) $blotter->resident_is_read
                ];
            }) : collect();

        // Ensure both are collections before merging
        $allUnread = collect($docUnread)->merge(collect($blotterUnread))->sortByDesc('created_at')->values()->take(5);
        $total = collect($docUnread)->count() + collect($blotterUnread)->count();
        
        return response()->json(['total' => $total, 'notifications' => $allUnread]);
    }

    public function markAsRead($id)
    {
        try {
            $userId = Session::get('user_id');
            
            // Try to find document request first
            $doc = DocumentRequest::where('resident_id', $userId)->where('id', $id)->first();
            if ($doc) {
                $doc->resident_is_read = true;
                $doc->save();
                return response()->json(['message' => 'Notification marked as read.']);
            }
            
            // If not found, try blotter request
            $resident = \App\Models\Residents::find($userId);
            $complainantName = $resident ? $resident->name : null;
            // Query blotters by complainant_name since residents see blotters they filed
            $blotter = $complainantName ? BlotterRequest::where('complainant_name', $complainantName)->where('id', $id)->first() : null;
            if ($blotter) {
                $blotter->resident_is_read = true;
                $blotter->save();
                return response()->json(['message' => 'Notification marked as read.']);
            }
            
            return response()->json(['message' => 'Not found'], 404);
        } catch (\Exception $e) {
            Log::error('Resident markAsRead error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed'], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $userId = Session::get('user_id');
            
            // Mark all approved document requests as read
            DocumentRequest::where('resident_id', $userId)
                ->where('status', 'approved')
                ->update(['resident_is_read' => true]);
                
            // Mark all approved blotter requests as read
            $resident = \App\Models\Residents::find($userId);
            $complainantName = $resident ? $resident->name : null;
            // Query blotters by complainant_name since residents see blotters they filed
            if ($complainantName) {
                BlotterRequest::where('complainant_name', $complainantName)
                    ->where('status', 'approved')
                    ->update(['resident_is_read' => true]);
            }
                
            notify()->success('All notifications marked as read.');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Resident markAllAsRead error: ' . $e->getMessage());
            notify()->error('Failed to mark all as read.');
            return redirect()->back();
        }
    }
}


