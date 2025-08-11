<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ResidentBlotterController
{
    public function requestBlotter()
    {
        return view('resident.request_blotter_report');
    }

    public function storeBlotter(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4,avi,mov,wmv|max:10240', // 10MB max per file
        ]);
        $userId = Session::get('user_id');
        if (!$userId) {
            Log::warning('Resident user ID not found in session for blotter submission.');
            notify()->error('You must be logged in to submit a report.');
            return redirect()->route('landing');
        }
        $blotter = new BlotterRequest();
        $blotter->resident_id = $userId;
        $blotter->recipient_name = $request->recipient_name;
        $blotter->type = $request->type;
        $blotter->description = $request->description;
        $blotter->status = 'pending';
        $blotter->attempts = 0;
        // Handle multiple file uploads
        if ($request->hasFile('media')) {
            $mediaFiles = [];
            foreach ($request->file('media') as $file) {
                $path = $file->store('blotter_media', 'public');
                $mediaFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
            $blotter->media = $mediaFiles;
        }
        try {
            $blotter->save();
            notify()->success('Blotter report submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            Log::error("Error creating blotter report: " . $e->getMessage());
            notify()->error('Error creating blotter: ' . $e->getMessage());
            return back();
        }
    }
} 