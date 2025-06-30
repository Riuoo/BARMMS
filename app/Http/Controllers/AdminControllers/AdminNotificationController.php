<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationController
{
    /**
     * Show the notifications page.
     *
     * @return \Illuminate\View\View
     */
    public function showNotifications()
    {
        // Fetch all notifications (read and unread) for the dedicated page
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
     * Get notification counts for the header dropdown.
     * This method is already used by ViewComposerServiceProvider.
     * We'll adjust it to count unread items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationCounts()
    {
        $pendingBlotterReports = BlotterRequest::where('status', 'pending')->where('is_read', false)->count();
        $pendingDocumentRequests = DocumentRequest::where('status', 'pending')->where('is_read', false)->count();
        $pendingAccountRequests = AccountRequest::where('status', 'pending')->where('is_read', false)->count();

        $totalPendingNotifications = $pendingBlotterReports + $pendingDocumentRequests + $pendingAccountRequests;

        return response()->json([
            'blotter_reports' => $pendingBlotterReports,
            'document_requests' => $pendingDocumentRequests,
            'account_requests' => $pendingAccountRequests,
            'total' => $totalPendingNotifications,
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
}

