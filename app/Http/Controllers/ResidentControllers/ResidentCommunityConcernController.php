<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\CommunityConcern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResidentCommunityConcernController extends BaseResidentRequestController
{
    public function requestCommunityConcern()
    {
        return view('resident.request_community_concern');
    }

    public function storeCommunityConcern(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4,avi,mov,wmv|max:10240', // 10MB max per file
        ]);

        $userId = $this->ensureAuthenticated();
        if (!$userId) {
            return redirect()->route('landing');
        }

        // Prevent multiple ongoing concerns
        if ($this->checkExistingRequests(CommunityConcern::class, $userId, ['pending', 'in_progress', 'under_review'], 'community concern')) {
            return back();
        }

        $concern = new CommunityConcern();
        $concern->resident_id = $userId;
        $concern->title = $request->title;
        $concern->description = $request->description;
        $concern->location = $request->location;
        $concern->status = 'pending';

        // Handle multiple file uploads
        $concern->media = $this->handleMediaUploads($request, 'concern_media');

        return $this->handleRequestCreation(
            $concern,
            'Community concern submitted successfully.',
            'resident.my-requests'
        );
    }

    public function getDetails($id)
    {
        try {
            $userId = $this->ensureAuthenticated();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $concern = CommunityConcern::with('resident')->findOrFail($id);
            
            // Ensure resident can only view their own concerns
            if ($concern->resident_id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Prepare media files for response
            $mediaFiles = null;
            if ($concern->media) {
                $mediaFiles = [];
                foreach ($concern->media as $file) {
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
            if ($concern->status === 'resolved' && $concern->resolved_at) {
                $statusChangedAt = $concern->resolved_at;
            } elseif ($concern->status === 'closed' && $concern->closed_at) {
                $statusChangedAt = $concern->closed_at;
            } else {
                $statusChangedAt = $concern->updated_at ?? $concern->created_at;
            }

            return response()->json([
                'title' => $concern->title,
                'description' => $concern->description,
                'location' => $concern->location,
                'status' => $concern->status,
                'created_at' => $concern->created_at->format('M d, Y \a\t g:i A'),
                'assigned_at' => $concern->assigned_at ? $concern->assigned_at->format('M d, Y \a\t g:i A') : 'Not assigned',
                'resolved_at' => $concern->resolved_at ? $concern->resolved_at->format('M d, Y \a\t g:i A') : 'Not resolved',
                'closed_at' => $concern->closed_at ? $concern->closed_at->format('M d, Y \a\t g:i A') : 'Not closed',
                'admin_remarks' => $concern->admin_remarks,
                'remarks_timestamp' => $statusChangedAt ? $statusChangedAt->format('M d, Y \a\t g:i A') : null,
                'media_files' => $mediaFiles,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching concern details for resident: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch details'], 500);
        }
    }
} 