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
            'document_type' => 'nullable|string|max:255',
            'description' => 'required|string',
            'document_template_id' => 'required|exists:document_templates,id',
            'template_fields' => 'nullable|array',
            'template_fields.*' => 'nullable|string|max:1000',
            'privacy_consent' => 'required|accepted',
        ]);

        $userId = $this->ensureAuthenticated();
        if (!$userId) {
            return redirect()->route('landing');
        }

        // Prevent multiple ongoing requests
        if ($this->checkExistingRequests(DocumentRequest::class, $userId, ['pending', 'processing'], 'document request')) {
            return back();
        }

        // Get template
        $template = DocumentTemplate::find($validated['document_template_id']);
        if (!$template) {
            return back()->withErrors(['document_template_id' => 'Selected template does not exist.']);
        }

        // Special validation for Barangay Clearance - require remarks and birth_place
        $isBarangayClearance = strtolower($template->document_type) === 'barangay clearance';
        if ($isBarangayClearance) {
            $request->validate([
                'template_fields.remarks' => 'required|string|max:1000',
                'template_fields.birth_place' => 'required|string|max:255',
            ], [
                'template_fields.remarks.required' => 'Remarks field is required for Barangay Clearance.',
                'template_fields.birth_place.required' => 'Birth Place field is required for Barangay Clearance.',
            ]);
        }

        // Prepare additional_data from template_fields
        $additionalData = [];
        if (!empty($validated['template_fields'])) {
            // Filter out empty values and clean the array
            $additionalData = array_filter($validated['template_fields'], function($value) {
                return $value !== null && $value !== '';
            });
        }

        $documentRequest = new DocumentRequest();
        $documentRequest->resident_id = $userId;
        $documentRequest->document_type = $validated['document_type'] ?? $template->document_type;
        $documentRequest->document_template_id = $template->id;
        $documentRequest->description = $validated['description'];
        $documentRequest->additional_data = !empty($additionalData) ? $additionalData : null;
        $documentRequest->status = 'pending';

        return $this->handleRequestCreation(
            $documentRequest,
            'Document request submitted successfully.',
            'resident.my-requests'
        );
    }
} 