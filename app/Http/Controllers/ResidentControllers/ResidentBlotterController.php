<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use Illuminate\Http\Request;

class ResidentBlotterController extends BaseResidentRequestController
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

        $userId = $this->ensureAuthenticated();
        if (!$userId) {
            return redirect()->route('landing');
        }

        // Prevent multiple ongoing requests
        if ($this->checkExistingRequests(BlotterRequest::class, $userId, ['pending', 'processing'], 'blotter request')) {
            return back();
        }

        $blotter = new BlotterRequest();
        $blotter->resident_id = $userId;
        $blotter->recipient_name = $request->recipient_name;
        $blotter->type = $request->type;
        $blotter->description = $request->description;
        $blotter->status = 'pending';
        $blotter->attempts = 0;

        // Handle multiple file uploads
        $blotter->media = $this->handleMediaUploads($request, 'blotter_media');

        return $this->handleRequestCreation(
            $blotter,
            'Blotter report submitted successfully.',
            'resident.my-requests'
        );
    }
} 