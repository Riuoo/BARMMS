<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ResidentDocumentRequestController
{
    public function requestDocument()
    {
        return view('resident.request_document_request');
    }

    public function storeDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $userId = Session::get('user_id');
        if (!$userId) {
            Log::warning('Resident user ID not found in session for document submission.');
            notify()->error('You must be logged in to submit a document request.');
            return redirect()->route('landing');
        }
        $documentRequest = new DocumentRequest();
        $documentRequest->user_id = $userId;
        $documentRequest->document_type = $request->document_type;
        $documentRequest->description = $request->description;
        $documentRequest->status = 'pending';
        try {
            $documentRequest->save();
            notify()->success('Document request submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            Log::error("Error creating document request: " . $e->getMessage());
            notify()->error('Error creating document request: ' . $e->getMessage());
            return back();
        }
    }
} 