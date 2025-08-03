<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\CommunityComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ResidentCommunityComplaintController
{
    public function requestCommunityComplaint()
    {
        return view('resident.request_community_complaint');
    }

    public function storeCommunityComplaint(Request $request)
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
            Log::warning('Resident user ID not found in session for complaint submission.');
            notify()->error('You must be logged in to submit a complaint.');
            return redirect()->route('landing');
        }
        $complaint = new CommunityComplaint();
        $complaint->user_id = $userId;
        $complaint->title = $request->title;
        $complaint->category = $request->category;
        $complaint->description = $request->description;
        $complaint->location = $request->location;
        $complaint->status = 'pending';
        // Handle multiple file uploads
        if ($request->hasFile('media')) {
            $mediaFiles = [];
            foreach ($request->file('media') as $file) {
                $path = $file->store('complaint_media', 'public');
                $mediaFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
            $complaint->media = $mediaFiles;
        }
        try {
            $complaint->save();
            notify()->success('Community complaint submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            Log::error("Error creating community complaint: " . $e->getMessage());
            notify()->error('Error creating complaint: ' . $e->getMessage());
            return back();
        }
    }
} 