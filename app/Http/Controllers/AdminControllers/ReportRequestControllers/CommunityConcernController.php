<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\CommunityConcern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CommunityConcernController
{
    public function index(Request $request)
    {
        // Statistics from full dataset (cache for 5 min)
        $total = Cache::remember('total_community_concerns', 300, function() {
            return CommunityConcern::count();
        });
        $pending = Cache::remember('pending_community_concerns', 300, function() {
            return CommunityConcern::where('status', 'pending')->count();
        });
        $under_review = Cache::remember('under_review_community_concerns', 300, function() {
            return CommunityConcern::where('status', 'under_review')->count();
        });
        $in_progress = Cache::remember('in_progress_community_concerns', 300, function() {
            return CommunityConcern::where('status', 'in_progress')->count();
        });
        $resolved = Cache::remember('resolved_community_concerns', 300, function() {
            return CommunityConcern::where('status', 'resolved')->count();
        });
        $closed = Cache::remember('closed_community_concerns', 300, function() {
            return CommunityConcern::where('status', 'closed')->count();
        });

        // For display (filtered)
        $query = CommunityConcern::with('resident');
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        // Only select needed columns for paginated concerns and eager load resident
        $concerns = $query->select([
            'id', 'resident_id', 'title', 'status', 'created_at'
        ])->with(['resident:id,name'])->orderByRaw("FIELD(status, 'pending', 'under_review', 'in_progress', 'resolved', 'closed')")
        ->orderByDesc('created_at')->paginate(10);
        $stats = [
            'total' => $total,
            'pending' => $pending,
            'under_review' => $under_review,
            'in_progress' => $in_progress,
            'resolved' => $resolved,
            'closed' => $closed,
        ];
        return view('admin.requests.community-concerns', compact('concerns', 'stats'));
    }

    public function getDetails($id)
    {
        try {
            $complaint = CommunityConcern::with('resident')->findOrFail($id);
            
            // Mark as read
            if (!$complaint->is_read) {
                $complaint->update(['is_read' => true]);
            }
            
            // Prepare media files for response
            $mediaFiles = null;
            if ($complaint->media) {
                $mediaFiles = [];
                foreach ($complaint->media as $file) {
                    $mediaFiles[] = [
                        'name' => $file['name'] ?? 'Attached File',
                        'url' => asset('storage/' . $file['path']),
                        'type' => $file['type'] ?? 'unknown',
                        'size' => $file['size'] ?? 0,
                    ];
                }
            }
            
            // Determine the status change timestamp relevant to the current status
            $statusChangedAt = null;
            if ($complaint->status === 'resolved' && $complaint->resolved_at) {
                $statusChangedAt = $complaint->resolved_at;
            } elseif ($complaint->status === 'closed' && $complaint->closed_at) {
                $statusChangedAt = $complaint->closed_at;
            } else {
                $statusChangedAt = $complaint->updated_at ?? $complaint->created_at;
            }

            return response()->json([
                'user_name' => $complaint->resident->name ?? 'N/A',
                'admin_remarks' => $complaint->admin_remarks,
                'remarks_timestamp' => $statusChangedAt ? $statusChangedAt->format('M d, Y \a\t g:i A') : null,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'location' => $complaint->location,
                'status' => $complaint->status,
                'created_at' => $complaint->created_at->format('M d, Y \a\t g:i A'),
                'assigned_at' => $complaint->assigned_at ? $complaint->assigned_at->format('M d, Y \a\t g:i A') : 'Not assigned',
                'resolved_at' => $complaint->resolved_at ? $complaint->resolved_at->format('M d, Y \a\t g:i A') : 'Not resolved',
                'closed_at' => $complaint->closed_at ? $complaint->closed_at->format('M d, Y \a\t g:i A') : 'Not closed',
                'media_files' => $mediaFiles,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching complaint details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch details'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,in_progress,resolved,closed',
            'admin_remarks' => 'nullable|string|max:1000',
        ]);
        try {
            $complaint = CommunityConcern::findOrFail($id);
            $user = $complaint->resident;
            if (!$user) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'This resident record no longer exists.'], 422);
                }
                notify()->error('This resident record no longer exists.');
                return redirect()->back();
            }
            if ($user->active === false) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'This user account is inactive and cannot make transactions.'], 422);
                }
                notify()->error('This user account is inactive and cannot make transactions.');
                return redirect()->back();
            }
            
            // Require remarks when marking as resolved or closed
            if (in_array($validated['status'], ['resolved', 'closed'])) {
                $request->validate([
                    'admin_remarks' => 'required|string|max:1000',
                ]);
            }
            
            $complaint->status = $validated['status'];
            
            // Set timestamps based on status
            if ($validated['status'] === 'under_review' && !$complaint->assigned_at) {
                $complaint->assigned_at = now();
            }
            
            if ($validated['status'] === 'resolved' && !$complaint->resolved_at) {
                $complaint->resolved_at = now();
            }

            if ($validated['status'] === 'closed' && !$complaint->closed_at) {
                $complaint->closed_at = now();
            }

            // Save admin remarks if provided
            if (array_key_exists('admin_remarks', $validated)) {
                $complaint->admin_remarks = $validated['admin_remarks'];
            }
            
            $complaint->save();

            // If the request is AJAX/JSON, return JSON so frontend can handle notify + reload
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            notify()->success('Concern status updated successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error("Error updating complaint status: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update concern status: ' . $e->getMessage()], 500);
            }
            notify()->error('Failed to update concern status: ' . $e->getMessage());
            return redirect()->back();
        }
    }
} 