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
            'respondent_id' => 'required|exists:residents,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4,avi,mov,wmv|max:10240', // 10MB max per file
        ]);

        $userId = $this->ensureAuthenticated();
        if (!$userId) {
            return redirect()->route('landing');
        }

        // Get the logged-in resident (complainant)
        $complainant = \App\Models\Residents::find($userId);
        if (!$complainant) {
            return redirect()->route('landing');
        }

        // Prevent multiple ongoing requests (check by complainant_name since resident_id is now the respondent)
        $existing = BlotterRequest::where('complainant_name', $complainant->name)
            ->whereIn('status', ['pending', 'processing'])
            ->first();
            
        if ($existing) {
            notify()->error('You already have an ongoing blotter request. Please wait until it is resolved before submitting another.');
            return back();
        }

        $blotter = new BlotterRequest();
        $blotter->complainant_name = $complainant->name; // The person filing the report
        $blotter->resident_id = $request->respondent_id; // The respondent (person being reported)
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