<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Residents;

class DocumentRequestController
{
    public function documentRequest()
    {
        // Eager load the user relationship
        $documentRequests = DocumentRequest::with('user')->get();
        return view('admin.document-requests', compact('documentRequests'));
    }

    public function approve($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if ($documentRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Document request already processed.');
            }

            $documentRequest->status = 'approved';
            $documentRequest->save();

            return redirect()->back()->with('success', 'Document request approved successfully.');
        } catch (\Exception $e) {
            Log::error('Error approving document request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve document request.');
        }
    }

    // NEW METHOD: Generate PDF for a document request
    public function generatePdf(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            // Update the status to completed
            $documentRequest->status = 'completed';
            $documentRequest->save();
            // Generate the PDF
            $pdf = Pdf::loadView('admin.pdfs.document_request_pdf', compact('documentRequest'));
            return $pdf->download('document_request_' . $documentRequest->id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating document request PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $residents = Residents::all();
        return view('admin.create_document_request', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:residents,id', // Ensure the user_id exists in the residents table
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        try {
            $documentRequest = DocumentRequest::create([
                'user_id' => $validated['user_id'],
                'document_type' => $validated['document_type'],
                'description' => $validated['description'],
                'status' => 'completed',
            ]);
            // Automatically generate and download the PDF after creation
            $pdf = Pdf::loadView('admin.pdfs.document_request_pdf', compact('documentRequest'));
            $filename = str_replace(' ', '_', strtolower($documentRequest->document_type)) . '_' . $documentRequest->id . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error creating document request: ' . $e->getMessage());
            return back()->with('error', 'Error creating document request: ' . $e->getMessage())->withInput();
        }
    }
}
