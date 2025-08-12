<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ResidentNotificationController
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('landing');
        }

        $docs = DocumentRequest::where('resident_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

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
        try {
            $userId = Session::get('user_id');
            if (!$userId) {
                return response()->json(['total' => 0, 'notifications' => []]);
            }

            $unreadDocs = DocumentRequest::where('resident_id', $userId)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->whereNull('resident_is_read')->orWhere('resident_is_read', false);
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            $data = $unreadDocs->map(function ($d) {
                return [
                    'id' => $d->id,
                    'type' => 'document_request',
                    'message' => 'Your ' . $d->document_type . ' is ready for pickup.',
                    'created_at' => Carbon::parse($d->updated_at)->toDateTimeString(),
                    'link' => route('resident.my-requests'),
                    'priority' => 'medium',
                ];
            })->values();

            return response()->json([
                'total' => $data->count(),
                'notifications' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Resident notifications count error: ' . $e->getMessage());
            return response()->json(['total' => 0, 'notifications' => []], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $userId = Session::get('user_id');
            $doc = DocumentRequest::where('resident_id', $userId)->where('id', $id)->first();
            if (!$doc) {
                return response()->json(['message' => 'Not found'], 404);
            }
            $doc->resident_is_read = true;
            $doc->save();
            return response()->json(['message' => 'Notification marked as read.']);
        } catch (\Exception $e) {
            Log::error('Resident markAsRead error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed'], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $userId = Session::get('user_id');
            DocumentRequest::where('resident_id', $userId)
                ->where('status', 'approved')
                ->update(['resident_is_read' => true]);
            return redirect()->back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            Log::error('Resident markAllAsRead error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark all as read.');
        }
    }
}


