<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

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
        return view('admin.requests.document-requests', compact('documentRequests'));
    }

    public function getDetails($id)
    {
        try {
            $documentRequest = DocumentRequest::with('user')->findOrFail($id);
            
            return response()->json([
                'user_name' => $documentRequest->user->name ?? 'N/A',
                'document_type' => $documentRequest->document_type,
                'purpose' => $documentRequest->description,
                'status' => $documentRequest->status,
                'created_at' => $documentRequest->created_at->format('M d, Y \a\t g:i A'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching document request details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch details'], 500);
        }
    }

    public function approve($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if ($documentRequest->status !== 'pending') {
                notify()->error('Document request already processed.');
                return redirect()->back();
            }

            $documentRequest->status = 'approved';
            $documentRequest->save();

            // Notify the resident that their document is ready for pickup
            if ($documentRequest->user) {
                $documentRequest->user->notify(new \App\Notifications\DocumentRequestApproved());
            }

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
            }

            // Log the document request for debugging
            Log::info('Document request details (approve): ' . json_encode([
                'id' => $documentRequest->id,
                'document_type' => $documentRequest->document_type,
                'status' => $documentRequest->status
            ]));

            // Determine the appropriate PDF template based on document type
            $template = $this->getPdfTemplate($documentRequest->document_type, $documentRequest);
            
            // Validate template exists
            if (empty($template)) {
                throw new \Exception('PDF template not found for document type: ' . $documentRequest->document_type);
            }
            
            // If the template is HTML (from database), load it directly
            if (strpos($template, '<!DOCTYPE html>') === 0) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($template);
            } else {
                // Check if the view exists
                if (!view()->exists($template)) {
                    Log::error('View does not exist: ' . $template);
                    throw new \Exception('PDF template view does not exist: ' . $template);
                }
                
                // Load the view template
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($template, [
                    'documentRequest' => $documentRequest,
                    'adminUser' => $adminUser
                ]);
            }
            
            // Generate filename based on document type
            $filename = $this->generateFilename($documentRequest);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error approving document request: ' . $e->getMessage());
            notify()->error('Failed to approve document request.');
            return redirect()->back();
        }
    }

    // NEW METHOD: Generate PDF for a document request
    public function generatePdf(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            
            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
            }
            
            // Log the document request for debugging
            Log::info('Document request details (generatePdf): ' . json_encode([
                'id' => $documentRequest->id,
                'document_type' => $documentRequest->document_type,
                'status' => $documentRequest->status
            ]));
            
            // Determine the appropriate PDF template based on document type
            $template = $this->getPdfTemplate($documentRequest->document_type, $documentRequest);
            
            // Validate template exists
            if (empty($template)) {
                throw new \Exception('PDF template not found for document type: ' . $documentRequest->document_type);
            }
            
            // Load the HTML template
            $pdf = Pdf::loadHTML($template);
            
            // Generate filename based on document type
            $filename = $this->generateFilename($documentRequest);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error generating document request PDF: ' . $e->getMessage());
            notify()->error('Failed to generate PDF: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function markAsComplete($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            if ($documentRequest->status !== 'approved') {
                notify()->error('Only approved requests can be marked as completed.');
                return back();
            }
            $documentRequest->status = 'completed';
            $documentRequest->save();
            notify()->success('Document request marked as completed.');
            return back();
        } catch (\Exception $e) {
            Log::error('Error marking document request as completed: ' . $e->getMessage());
            notify()->error('Failed to mark as completed: ' . $e->getMessage());
            return back();
        }
    }

    public function create()
    {
        $residents = Residents::all();
        return view('admin.requests.create_document_request', compact('residents'));
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
                'status' => 'approved',
            ]);
            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
            }

            // Log the document request for debugging
            Log::info('Document request details (store): ' . json_encode([
                'id' => $documentRequest->id,
                'document_type' => $documentRequest->document_type,
                'status' => $documentRequest->status
            ]));

            // Determine the appropriate PDF template based on document type
            $template = $this->getPdfTemplate($documentRequest->document_type, $documentRequest);
            
            // Validate template exists
            if (empty($template)) {
                throw new \Exception('PDF template not found for document type: ' . $documentRequest->document_type);
            }
            
            // If the template is HTML (from database), load it directly
            if (strpos($template, '<!DOCTYPE html>') === 0) {
                $pdf = Pdf::loadHTML($template);
            } else {
                // Check if the view exists
                if (!view()->exists($template)) {
                    Log::error('View does not exist: ' . $template);
                    throw new \Exception('PDF template view does not exist: ' . $template);
                }
                
                // Load the view template
                $pdf = Pdf::loadView($template, [
                    'documentRequest' => $documentRequest,
                    'adminUser' => $adminUser
                ]);
            }
            
            // Generate filename based on document type
            $filename = $this->generateFilename($documentRequest);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error creating document request: ' . $e->getMessage());
            notify()->error('Error creating document request: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }
}
