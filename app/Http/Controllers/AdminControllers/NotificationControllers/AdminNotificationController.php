<?php

namespace App\Http\Controllers\AdminControllers\NotificationControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminNotificationController
{
    /**
     * Show the notifications page.
     *
     * @return \Illuminate\View\View
     */
    public function showNotifications(Request $request)
    {
        $blotterReports = BlotterRequest::orderBy('created_at', 'desc')->get();
        $documentRequests = DocumentRequest::orderBy('created_at', 'desc')->get();
        $accountRequests = AccountRequest::orderBy('created_at', 'desc')->get();

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

        // Filter by search
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $notifications = $notifications->filter(function($n) use ($search) {
                return strpos(strtolower($n->message), $search) !== false;
            });
        }
        // Filter by read/unread
        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $notifications = $notifications->where('is_read', false);
            } elseif ($request->read_status === 'read') {
                $notifications = $notifications->where('is_read', true);
            }
        }

        // Sort notifications by date (latest first)
        $notifications = $notifications->sortByDesc('created_at');

        return view('admin.health.notifications', compact('notifications'));
    }

    /**
     * Get individual unread notifications for the header dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */

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
                        'message' => 'New blotter report from ' . ($report->user->name ?? 'Unknown Resident'),
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
                        'message' => 'Document request from ' . ($request->user->name ?? 'Unknown Resident'),
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
                        'link' => route('admin.new-account-requests'),
                        'priority' => 'high'
                    ];
                } catch (\Exception $e) {
                    // Skip this notification if there's an error
                    Log::error('Error processing account request notification: ' . $e->getMessage());
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
            
            return response()->json([
                'total' => $sortedNotifications->count(),
                'notifications' => $limitedNotifications->toArray(),
                'summary' => [
                    'blotter_reports' => BlotterRequest::where('is_read', false)->count(),
                    'document_requests' => DocumentRequest::where('is_read', false)->count(),
                    'account_requests' => AccountRequest::where('is_read', false)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getNotificationCounts: ' . $e->getMessage());
            return response()->json([
                'total' => 0,
                'notifications' => [],
                'summary' => [
                    'blotter_reports' => 0,
                    'document_requests' => 0,
                    'account_requests' => 0,
                ],
                'error' => 'Failed to load notifications'
            ], 500);
        }
    }

    /**
     * Mark all reports and requests as read.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead(Request $request)
    {
        DB::beginTransaction();
        try {
            BlotterRequest::where('is_read', false)->update(['is_read' => true]);
            DocumentRequest::where('is_read', false)->update(['is_read' => true]);
            AccountRequest::where('is_read', false)->update(['is_read' => true]);
            DB::commit();
            notify()->success('All notifications marked as read.');
            return redirect()->back();
            
        } catch (\Exception $e) {
            DB::rollBack();
            notify()->error('Failed to mark all notifications as read: ' . $e->getMessage());
            return redirect()->back();
            
        }
    }

    /**
     * Mark a specific notification as read.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type The type of notification (e.g., 'blotter_report', 'document_request', 'account_request')
     * @param int $id The ID of the notification
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Mark all notifications as read via AJAX.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsReadAjax(Request $request)
    {
        DB::beginTransaction();
        try {
            BlotterRequest::where('is_read', false)->update(['is_read' => true]);
            DocumentRequest::where('is_read', false)->update(['is_read' => true]);
            AccountRequest::where('is_read', false)->update(['is_read' => true]);
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
}
