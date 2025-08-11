<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\DocumentRequest;
use App\Models\DocumentTemplate;
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
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
            'document_template_id' => 'nullable|exists:document_templates,id',
        ]);
        $userId = Session::get('user_id');
        if (!$userId) {
            Log::warning('Resident user ID not found in session for document submission.');
            notify()->error('You must be logged in to submit a document request.');
            return redirect()->route('landing');
        }
        // Prefer explicit template id from the request; otherwise resolve by document_type
        $templateId = $validated['document_template_id'] ?? optional(
            DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($validated['document_type']))])->first()
        )->id;

        $documentRequest = new DocumentRequest();
        $documentRequest->resident_id = $userId;
        $documentRequest->document_type = $validated['document_type'];
        $documentRequest->document_template_id = $templateId;
        $documentRequest->description = $validated['description'];
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