<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\DocumentRequest;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class ResidentDocumentRequestController extends BaseResidentRequestController
{
    public function requestDocument()
    {
        $templates = DocumentTemplate::where('is_active', true)
            ->orderBy('document_type')
            ->get();
        return view('resident.request_document_request', compact('templates'));
    }

    public function storeDocument(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
            'document_template_id' => 'nullable|exists:document_templates,id',
        ]);

        $userId = $this->ensureAuthenticated();
        if (!$userId) {
            return redirect()->route('landing');
        }

        // Prevent multiple ongoing requests
        if ($this->checkExistingRequests(DocumentRequest::class, $userId, ['pending', 'processing'], 'document request')) {
            return back();
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

        return $this->handleRequestCreation(
            $documentRequest,
            'Document request submitted successfully.',
            'resident.my-requests'
        );
    }
} 