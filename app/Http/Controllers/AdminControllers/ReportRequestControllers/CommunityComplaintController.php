<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\CommunityComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommunityComplaintController
{
    public function index(Request $request)
    {
        // Statistics from full dataset
        $total = CommunityComplaint::count();
        $pending = CommunityComplaint::where('status', 'pending')->count();
        $under_review = CommunityComplaint::where('status', 'under_review')->count();
        $in_progress = CommunityComplaint::where('status', 'in_progress')->count();
        $resolved = CommunityComplaint::where('status', 'resolved')->count();
        $closed = CommunityComplaint::where('status', 'closed')->count();

        // For display (filtered)
        $query = CommunityComplaint::with('user');
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
        $complaints = $query->orderByRaw("FIELD(status, 'pending', 'under_review', 'in_progress', 'resolved', 'closed')")->orderByDesc('created_at')->paginate(10);
        $stats = [
            'total' => $total,
            'pending' => $pending,
            'under_review' => $under_review,
            'in_progress' => $in_progress,
            'resolved' => $resolved,
            'closed' => $closed,
        ];
        return view('admin.requests.community-complaints', compact('complaints', 'stats'));
    }

    public function getDetails($id)
    {
        try {
            $complaint = CommunityComplaint::with('user')->findOrFail($id);
            
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
                'user_name' => $complaint->user->name ?? 'N/A',
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
            $complaint = CommunityComplaint::findOrFail($id);
            $user = $complaint->user;
            if (!$user || !$user->active) {
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

            notify()->success('Complaint status updated successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error("Error updating complaint status: " . $e->getMessage());
            notify()->error('Failed to update complaint status: ' . $e->getMessage());
            return redirect()->back();
        }
    }
} 