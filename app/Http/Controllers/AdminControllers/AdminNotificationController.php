<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminNotificationController
{
    /**
     * Show the notifications page.
     *
     * @return \Illuminate\View\View
     */
    public function showNotifications()
    {
        // This method remains largely the same as it fetches all notifications for the dedicated page
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
                'link' => route('admin.blotter-reports'), // Link to the specific report type page
            ]);
        }

        foreach ($documentRequests as $request) {
            $notifications->push((object)[
                'id' => $request->id,
                'type' => 'document_request',
                'message' => 'New document request pending approval.',
                'created_at' => $request->created_at,
                'is_read' => $request->is_read,
                'link' => route('admin.document-requests'), // Link to the specific request type page
            ]);
        }

        foreach ($accountRequests as $request) {
            $notifications->push((object)[
                'id' => $request->id,
                'type' => 'account_request',
                'message' => 'New account request awaiting action.',
                'created_at' => $request->created_at,
                'is_read' => $request->is_read,
                'link' => route('admin.new-account-requests'), // Link to the specific request type page
            ]);
        }

        // Sort notifications by date (latest first)
        $notifications = $notifications->sortByDesc('created_at');

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Get individual unread notifications for the header dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getNotificationCounts()
    {
        $notificationsData = []; // Initialize as a plain PHP array
        // Process Blotter Reports
        BlotterRequest::where('is_read', false)->get()->each(function ($report) use (&$notificationsData) { // Use & for reference
            $notificationsData[] = [ // Append to plain array
                'id' => $report->id,
                'type' => 'blotter_report',
                'message' => 'New blotter report pending review.',
                'created_at' => Carbon::parse($report->created_at)->toDateTimeString(),
                'link' => route('admin.blotter-reports'),
            ];
        });
        // Process Document Requests
        DocumentRequest::where('is_read', false)->get()->each(function ($request) use (&$notificationsData) {
            $notificationsData[] = [
                'id' => $request->id,
                'type' => 'document_request',
                'message' => 'New document request pending approval.',
                'created_at' => Carbon::parse($request->created_at)->toDateTimeString(),
                'link' => route('admin.document-requests'),
            ];
        });

        // Process Account Requests
        AccountRequest::where('is_read', false)->get()->each(function ($request) use (&$notificationsData) {
            $notificationsData[] = [
                'id' => $request->id,
                'type' => 'account_request',
                'message' => 'New account request awaiting action.',
                'created_at' => Carbon::parse($request->created_at)->toDateTimeString(),
                'link' => route('admin.new-account-requests'),
            ];
        });
        // Convert the plain array to a Laravel Collection for sorting
        $allUnreadNotifications = collect($notificationsData);
        // Sort notifications by date (latest first)
        $sortedNotifications = $allUnreadNotifications->sortByDesc('created_at')->values();
        // Limit the number of notifications shown in the dropdown
        $limitedNotifications = $sortedNotifications->take(5);
        return response()->json([
            'total' => $sortedNotifications->count(),
            'notifications' => $limitedNotifications->toArray(), // Ensure it's a plain array for JSON
        ]);
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
            return redirect()->back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark all notifications as read: ' . $e->getMessage());
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
            switch ($type) {
                case 'blotter_report':
                    BlotterRequest::where('id', $id)->update(['is_read' => true]);
                    break;
                case 'document_request':
                    DocumentRequest::where('id', $id)->update(['is_read' => true]);
                    break;
                case 'account_request':
                    AccountRequest::where('id', $id)->update(['is_read' => true]);
                    break;
                default:
                    return response()->json(['message' => 'Invalid notification type.'], 400);
            }
            return response()->json(['message' => 'Notification marked as read.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to mark notification as read: ' . $e->getMessage()], 500);
        }
    }
}
