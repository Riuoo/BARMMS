<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use Illuminate\Http\Request;

class ResidentBlotterController extends BaseResidentRequestController
{
    public function requestBlotter()
    {
        $userId = $this->getResidentId();
        return view('resident.request_blotter_report', ['currentUserId' => $userId]);
    }

    public function storeBlotter(Request $request)
    {
        $request->validate(
            [
                'respondent_id' => 'required|exists:residents,id',
                'type' => 'required|string|max:255',
                'description' => 'required|string',
                'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4,avi,mov,wmv|max:10240', // 10MB max per file
            ],
            [
                'respondent_id.required' => 'Please select a registered respondent.',
                'respondent_id.exists' => 'Selected respondent was not found.',
            ]
        );

        $userId = $this->ensureAuthenticated();
        if (!$userId) {
            return redirect()->route('landing');
        }

        // Get the logged-in resident (complainant)
        $complainant = \App\Models\Residents::find($userId);
        if (!$complainant) {
            return redirect()->route('landing');
        }

        // Prevent same person from being both complainant and respondent
        if ($userId == $request->respondent_id) {
            notify()->error('You cannot file a blotter report against yourself. Please select a different person as the respondent.');
            return back()->withInput();
        }

        // Prevent multiple ongoing requests (check by complainant_name since resident_id is now the respondent)
        $existing = BlotterRequest::where('complainant_name', $complainant->full_name)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existing) {
            notify()->error('You already have an ongoing blotter request. Please wait until it is resolved before submitting another.');
            return back();
        }
        
        // Also check if the respondent has an ongoing request
        $existingRespondentRequest = BlotterRequest::where('respondent_id', $request->respondent_id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existingRespondentRequest) {
            notify()->error('The selected respondent already has an ongoing blotter request. Please wait until it is resolved before submitting another.');
            return back();
        }

        $blotter = new BlotterRequest();
        $blotter->complainant_name = $complainant->full_name; // The person filing the report
        $blotter->respondent_id = $request->respondent_id; // The respondent (person being reported)
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