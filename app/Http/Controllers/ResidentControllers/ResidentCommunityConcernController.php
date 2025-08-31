<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\CommunityConcern;
use Illuminate\Http\Request;

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
            'category' => 'required|string',
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
        $concern->category = $request->category;
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
} 