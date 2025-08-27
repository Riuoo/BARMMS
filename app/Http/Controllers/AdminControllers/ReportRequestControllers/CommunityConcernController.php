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
            'id', 'resident_id', 'title', 'category', 'status', 'created_at'
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
            
            return response()->json([
                'user_name' => $complaint->resident->name ?? 'N/A',
                'title' => $complaint->title,
                'category' => $complaint->category,
                'description' => $complaint->description,
                'location' => $complaint->location,
                'status' => $complaint->status,
                'created_at' => $complaint->created_at->format('M d, Y \a\t g:i A'),
                'assigned_at' => $complaint->assigned_at ? $complaint->assigned_at->format('M d, Y \a\t g:i A') : 'Not assigned',
                'resolved_at' => $complaint->resolved_at ? $complaint->resolved_at->format('M d, Y \a\t g:i A') : 'Not resolved',
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
        ]);
        try {
            $complaint = CommunityConcern::findOrFail($id);
            $user = $complaint->resident;
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return redirect()->back();
            }
            if ($user->active === false) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return redirect()->back();
            }
            
            $complaint->status = $validated['status'];
            
            // Set timestamps based on status
            if ($validated['status'] === 'under_review' && !$complaint->assigned_at) {
                $complaint->assigned_at = now();
            }
            
            if ($validated['status'] === 'resolved' && !$complaint->resolved_at) {
                $complaint->resolved_at = now();
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
                return response()->json(['success' => false, 'message' => 'Failed to update concern status'], 500);
            }
            notify()->error('Failed to update concern status: ' . $e->getMessage());
            return redirect()->back();
        }
    }
} 