<?php

namespace App\Http\Controllers\AdminControllers\NotificationControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\AccountRequest;
use App\Models\CommunityComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminNotificationController
{
    public function showNotifications(Request $request)
    {
        // Build query for each notification type
        $blotterQuery = BlotterRequest::query();
        $documentQuery = DocumentRequest::query();
        $accountQuery = AccountRequest::query();
        $complaintQuery = CommunityComplaint::query();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            
            // Search in blotter reports
            $blotterQuery->where(function($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
            
            // Search in document requests
            $documentQuery->where(function($q) use ($search) {
                $q->where('document_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            
            // Search in account requests
            $accountQuery->where('email', 'like', "%{$search}%");
            
            // Search in community complaints
            $complaintQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Apply read status filter if provided
        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $blotterQuery->where('is_read', false);
                $documentQuery->where('is_read', false);
                $accountQuery->where('is_read', false);
                $complaintQuery->where('is_read', false);
            } elseif ($request->read_status === 'read') {
                $blotterQuery->where('is_read', true);
                $documentQuery->where('is_read', true);
                $accountQuery->where('is_read', true);
                $complaintQuery->where('is_read', true);
            }
        }

        // Get all results (not paginated yet)
        $blotterReports = $blotterQuery->orderBy('created_at', 'desc')->get();
        $documentRequests = $documentQuery->orderBy('created_at', 'desc')->get();
        $accountRequests = $accountQuery->orderBy('created_at', 'desc')->get();
        $communityComplaints = $complaintQuery->orderBy('created_at', 'desc')->get();

        // Combine notifications into a single collection
        $notifications = collect();

        foreach ($blotterReports as $report) {
            $notifications->push((object)[
                'id' => $report->id,
                'type' => 'blotter_report',
                'message' => 'New blotter report pending review.',
                'created_at' => $report->created_at,
                'is_read' => $report->is_read,
                'link' => route('admin.blotter-reports'),
            ]);
        }

        foreach ($documentRequests as $requestDoc) {
            $notifications->push((object)[
                'id' => $requestDoc->id,
                'type' => 'document_request',
                'message' => 'New document request pending approval.',
                'created_at' => $requestDoc->created_at,
                'is_read' => $requestDoc->is_read,
                'link' => route('admin.document-requests'),
            ]);
        }

        foreach ($accountRequests as $requestAcc) {
            $notifications->push((object)[
                'id' => $requestAcc->id,
                'type' => 'account_request',
                'message' => 'New account request awaiting action.',
                'created_at' => $requestAcc->created_at,
                'is_read' => $requestAcc->is_read,
                'link' => route('admin.requests.new-account-requests'),
            ]);
        }

        foreach ($communityComplaints as $complaint) {
            $notifications->push((object)[
                'id' => $complaint->id,
                'type' => 'community_complaint',
                'message' => 'New community complaint pending review.',
                'created_at' => $complaint->created_at,
                'is_read' => $complaint->is_read,
                'link' => route('admin.community-complaints'),
            ]);
        }

        // Sort notifications by date (latest first)
        $notifications = $notifications->sortByDesc('created_at');

        // Create a custom paginator for the combined results
        $perPage = 20; // Show 20 notifications per page
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

        return view('admin.notifications.notifications', compact('notifications'));
    }

    public function getNotificationCounts()
    {
        try {
            $notificationsData = [];
            
            // Process Blotter Reports
            BlotterRequest::where('is_read', false)->get()->each(function ($report) use (&$notificationsData) {
                try {
                    $notificationsData[] = [
                        'id' => $report->id,
                        'type' => 'blotter_report',
                        'message' => 'New blotter report from ' . ($report->resident->name ?? 'Unknown Resident'),
                        'created_at' => Carbon::parse($report->created_at)->toDateTimeString(),
                        'link' => route('admin.blotter-reports'),
                        'priority' => 'high'
                    ];
                } catch (\Exception $e) {
                    // Skip this notification if there's an error
                    Log::error('Error processing blotter report notification: ' . $e->getMessage());
                }
            });
            
            // Process Document Requests
            DocumentRequest::where('is_read', false)->get()->each(function ($request) use (&$notificationsData) {
                try {
                    $notificationsData[] = [
                        'id' => $request->id,
                        'type' => 'document_request',
                        'message' => 'Document request from ' . ($request->resident->name ?? 'Unknown Resident'),
                        'created_at' => Carbon::parse($request->created_at)->toDateTimeString(),
                        'link' => route('admin.document-requests'),
                        'priority' => 'medium'
                    ];
                } catch (\Exception $e) {
                    // Skip this notification if there's an error
                    Log::error('Error processing document request notification: ' . $e->getMessage());
                }
            });

            // Process Account Requests
            AccountRequest::where('is_read', false)->get()->each(function ($request) use (&$notificationsData) {
                try {
                    $notificationsData[] = [
                        'id' => $request->id,
                        'type' => 'account_request',
                        'message' => 'Account request from ' . $request->email,
                        'created_at' => Carbon::parse($request->created_at)->toDateTimeString(),
                        'link' => route('admin.requests.new-account-requests'),
                        'priority' => 'high'
                    ];
                } catch (\Exception $e) {
                    // Skip this notification if there's an error
                    Log::error('Error processing account request notification: ' . $e->getMessage());
                }
            });
            
            // Process Community Complaints
            CommunityComplaint::where('is_read', false)->get()->each(function ($complaint) use (&$notificationsData) {
                try {
                    $notificationsData[] = [
                        'id' => $complaint->id,
                        'type' => 'community_complaint',
                        'message' => 'Community complaint from ' . ($complaint->resident->name ?? 'Unknown Resident'),
                        'created_at' => Carbon::parse($complaint->created_at)->toDateTimeString(),
                        'link' => route('admin.community-complaints'),
                        'priority' => 'medium'
                    ];
                } catch (\Exception $e) {
                    // Skip this notification if there's an error
                    Log::error('Error processing community complaint notification: ' . $e->getMessage());
                }
            });
            
            // Convert to collection and sort by priority and date
            $allUnreadNotifications = collect($notificationsData);
            
            // Sort by priority (high first) then by date (latest first)
            $sortedNotifications = $allUnreadNotifications->sortBy([
                ['priority', 'desc'],
                ['created_at', 'desc']
            ])->values();
            
            // Limit to 5 notifications for dropdown
            $limitedNotifications = $sortedNotifications->take(5);

            // Compute totals across models
            $unreadCounts = [
                'blotter_reports' => BlotterRequest::where('is_read', false)->count(),
                'document_requests' => DocumentRequest::where('is_read', false)->count(),
                'account_requests' => AccountRequest::where('is_read', false)->count(),
                'community_complaints' => CommunityComplaint::where('is_read', false)->count(),
            ];
            $readCounts = [
                'blotter_reports' => BlotterRequest::where('is_read', true)->count(),
                'document_requests' => DocumentRequest::where('is_read', true)->count(),
                'account_requests' => AccountRequest::where('is_read', true)->count(),
                'community_complaints' => CommunityComplaint::where('is_read', true)->count(),
            ];
            $totalCounts = [
                'blotter_reports' => BlotterRequest::count(),
                'document_requests' => DocumentRequest::count(),
                'account_requests' => AccountRequest::count(),
                'community_complaints' => CommunityComplaint::count(),
            ];

            $unreadTotal = array_sum($unreadCounts);
            $readTotal = array_sum($readCounts);
            $totalAll = array_sum($totalCounts);
            
            return response()->json([
                // Backwards compatibility (total previously meant unread)
                'total' => $unreadTotal,
                'total_unread' => $unreadTotal,
                'total_read' => $readTotal,
                'total_all' => $totalAll,
                'notifications' => $limitedNotifications->toArray(),
                'summary' => [
                    'unread' => $unreadCounts,
                    'read' => $readCounts,
                    'total' => $totalCounts,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getNotificationCounts: ' . $e->getMessage());
            return response()->json([
                'total' => 0,
                'total_unread' => 0,
                'total_read' => 0,
                'total_all' => 0,
                'notifications' => [],
                'summary' => [
                    'unread' => [
                        'blotter_reports' => 0,
                        'document_requests' => 0,
                        'account_requests' => 0,
                        'community_complaints' => 0,
                    ],
                    'read' => [
                        'blotter_reports' => 0,
                        'document_requests' => 0,
                        'account_requests' => 0,
                        'community_complaints' => 0,
                    ],
                    'total' => [
                        'blotter_reports' => 0,
                        'document_requests' => 0,
                        'account_requests' => 0,
                        'community_complaints' => 0,
                    ],
                ],
                'error' => 'Failed to load notifications'
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        DB::beginTransaction();
        try {
            BlotterRequest::where('is_read', false)->update(['is_read' => true]);
            DocumentRequest::where('is_read', false)->update(['is_read' => true]);
            AccountRequest::where('is_read', false)->update(['is_read' => true]);
            CommunityComplaint::where('is_read', false)->update(['is_read' => true]);
            DB::commit();
            notify()->success('All notifications marked as read.');
            return redirect()->back();
            
        } catch (\Exception $e) {
            DB::rollBack();
            notify()->error('Failed to mark all notifications as read: ' . $e->getMessage());
            return redirect()->back();
            
        }
    }

    public function markAsRead(Request $request, $type, $id)
    {
        try {
            $updated = false;
            
            switch ($type) {
                case 'blotter_report':
                    $updated = BlotterRequest::where('id', $id)->update(['is_read' => true]) > 0;
                    break;
                case 'document_request':
                    $updated = DocumentRequest::where('id', $id)->update(['is_read' => true]) > 0;
                    break;
                case 'account_request':
                    $updated = AccountRequest::where('id', $id)->update(['is_read' => true]) > 0;
                    break;
                case 'community_complaint':
                    $updated = CommunityComplaint::where('id', $id)->update(['is_read' => true]) > 0;
                    break;
                default:
                    return response()->json(['message' => 'Invalid notification type.'], 400);
            }
            
            if ($updated) {
                return response()->json(['message' => 'Notification marked as read.']);
            } else {
                return response()->json(['message' => 'Notification not found or already marked as read.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to mark notification as read: ' . $e->getMessage()], 500);
        }
    }
    
    public function markAllAsReadAjax(Request $request)
    {
        DB::beginTransaction();
        try {
            BlotterRequest::where('is_read', false)->update(['is_read' => true]);
            DocumentRequest::where('is_read', false)->update(['is_read' => true]);
            AccountRequest::where('is_read', false)->update(['is_read' => true]);
            CommunityComplaint::where('is_read', false)->update(['is_read' => true]);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read.',
                'total' => 0
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsReadByType(Request $request, $type)
    {
        try {
            $updated = false;
            
            switch ($type) {
                case 'blotter_report':
                    $updated = BlotterRequest::where('is_read', false)->update(['is_read' => true]) > 0;
                    break;
                case 'document_request':
                    $updated = DocumentRequest::where('is_read', false)->update(['is_read' => true]) > 0;
                    break;
                case 'account_request':
                    $updated = AccountRequest::where('is_read', false)->update(['is_read' => true]) > 0;
                    break;
                case 'community_complaint':
                    $updated = CommunityComplaint::where('is_read', false)->update(['is_read' => true]) > 0;
                    break;
                default:
                    return response()->json(['message' => 'Invalid notification type.'], 400);
            }
            
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'All ' . str_replace('_', ' ', $type) . ' notifications marked as read.'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No unread ' . str_replace('_', ' ', $type) . ' notifications found.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error marking notifications as read by type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }
}