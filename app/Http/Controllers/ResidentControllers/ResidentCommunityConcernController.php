<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\CommunityConcern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ResidentCommunityConcernController
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
        $userId = Session::get('user_id');
        if (!$userId) {
            Log::warning('Resident user ID not found in session for concern submission.');
            notify()->error('You must be logged in to submit a concern.');
            return redirect()->route('landing');
        }
        $concern = new CommunityConcern();
        $concern->resident_id = $userId;
        $concern->title = $request->title;
        $concern->category = $request->category;
        $concern->description = $request->description;
        $concern->location = $request->location;
        $concern->status = 'pending';
        // Handle multiple file uploads
        if ($request->hasFile('media')) {
            $mediaFiles = [];
            foreach ($request->file('media') as $file) {
                $path = $file->store('concern_media', 'public');
                $mediaFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
            $concern->media = $mediaFiles;
        }
        try {
            $concern->save();
            notify()->success('Community concern submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            Log::error("Error creating community concern: " . $e->getMessage());
            notify()->error('Error creating concern: ' . $e->getMessage());
            return back();
        }
    }
} 