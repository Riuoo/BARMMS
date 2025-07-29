<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Residents;
use App\Models\BarangayProfile;
use App\Models\DocumentTemplate;

class DocumentRequestController
{
    public function documentRequest(Request $request)
    {
        // Statistics from full dataset
        $totalRequests = DocumentRequest::count();
        $pendingCount = DocumentRequest::where('status', 'pending')->count();
        $approvedCount = DocumentRequest::where('status', 'approved')->count();
        $completedCount = DocumentRequest::where('status', 'completed')->count();

        // For display (filtered)
        $query = DocumentRequest::with('user');
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('document_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $documentRequests = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'completed')")->orderByDesc('created_at')->paginate(10);
        return view('admin.requests.document-requests', compact('documentRequests', 'totalRequests', 'pendingCount', 'approvedCount', 'completedCount'));
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

    // Add this method to process placeholders in the HTML template
    protected function processTemplatePlaceholders($html, $documentRequest, $adminUser)
    {
        $replacements = [
            '{{user_name}}' => $documentRequest->user ? $documentRequest->user->name : '',
            '{{document_type}}' => $documentRequest->document_type,
            '{{purpose}}' => $documentRequest->description,
            '{{admin_name}}' => $adminUser ? $adminUser->name : '',
            // Add more as needed
        ];
        return strtr($html, $replacements);
    }

    public function approve($id)
    {
        try {
            $documentRequest = DocumentRequest::with('user')->findOrFail($id);
            $user = $documentRequest->user;
            if (!$user || !$user->active) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return redirect()->back();
            }

            if ($documentRequest->status !== 'pending') {
                notify()->error('Document request already processed.');
                return redirect()->back();
            }

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = BarangayProfile::find(session('user_id'));
            }

            // Fetch the template from the database (case-insensitive, trimmed)
            $template = DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($documentRequest->document_type))])->first();

            if (!$template) {
                notify()->error('No template found for this document type.');
                return redirect()->back();
            }

            // Prepare values for placeholders
            $values = [
                'resident_name' => $documentRequest->user ? $documentRequest->user->name : '',
                'resident_address' => $documentRequest->user ? $documentRequest->user->address : '',
                'civil_status' => $documentRequest->user ? $documentRequest->user->civil_status : '',
                'purpose' => $documentRequest->description,
                'day' => date('jS'),
                'month' => date('F'),
                'year' => date('Y'),
                'barangay_name' => $adminUser ? $adminUser->barangay_name : '',
                'municipality_name' => $adminUser ? $adminUser->municipality_name : '',
                'province_name' => $adminUser ? $adminUser->province_name : '',
                'official_name' => $adminUser ? $adminUser->name : '',
                'official_position' => $adminUser ? ($adminUser->position ?? '') : '',
            ];

            // Generate the HTML using the template's generateHtml method
            $html = $template->generateHtml($values);

            // Generate the PDF
            $pdf = Pdf::loadHTML($html);

            // Mark as approved
            $documentRequest->status = 'approved';
            $documentRequest->save();

            // Download the PDF
            $filename = $this->generateFilename($documentRequest);
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error approving document request: ' . $e->getMessage());
            notify()->error('Failed to approve document request. ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // NEW METHOD: Generate PDF for a document request
    public function generatePdf(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::with('user')->findOrFail($id);
            $user = $documentRequest->user;
            if (!$user || !$user->active) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return redirect()->back();
            }

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = BarangayProfile::find(session('user_id'));
            }

            // Fetch the template from the database (case-insensitive, trimmed)
            $template = DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($documentRequest->document_type))])->first();

            if (!$template) {
                notify()->error('No template found for this document type.');
                return redirect()->back();
            }

            // Prepare values for placeholders
            $values = [
                'resident_name' => $documentRequest->user ? $documentRequest->user->name : '',
                'resident_address' => $documentRequest->user ? $documentRequest->user->address : '',
                'civil_status' => $documentRequest->user ? $documentRequest->user->civil_status : '',
                'purpose' => $documentRequest->description,
                'day' => date('jS'),
                'month' => date('F'),
                'year' => date('Y'),
                'barangay_name' => $adminUser ? $adminUser->barangay_name : '',
                'municipality_name' => $adminUser ? $adminUser->municipality_name : '',
                'province_name' => $adminUser ? $adminUser->province_name : '',
                'official_name' => $adminUser ? $adminUser->name : '',
                'official_position' => $adminUser ? ($adminUser->position ?? '') : '',
            ];

            // Generate the HTML using the template's generateHtml method
            $html = $template->generateHtml($values);

            // Generate the PDF
            $pdf = Pdf::loadHTML($html);

            // If status is 'approved', mark as 'completed'
            if ($documentRequest->status === 'approved') {
                $documentRequest->status = 'completed';
                $documentRequest->save();
            }

            // Download the PDF
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
            $user = $documentRequest->user;
            if (!$user || !$user->active) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return back();
            }
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
        $residents = Residents::where('active', true)->get();
        return view('admin.requests.create_document_request', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:residents,id',
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $user = Residents::find($validated['user_id']);
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
        }
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
                $adminUser = BarangayProfile::find(session('user_id'));
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

    // Add this method to select the PDF template based on document type
    protected function getPdfTemplate($documentType, $documentRequest)
    {
        $documentType = trim($documentType);
        Log::info('Looking for template with document_type: [' . $documentType . ']');
        $template = DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower($documentType)])->first();
        if ($template && $template->html) {
            return $template->html;
        }
        return '';
    }

    // Add this method to generate a filename for the PDF
    protected function generateFilename($documentRequest)
    {
        $type = preg_replace('/[^a-zA-Z0-9_\-]/', '_', strtolower($documentRequest->document_type));
        $user = $documentRequest->user ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', strtolower($documentRequest->user->name)) : 'unknown_user';
        $id = $documentRequest->id;
        return $type . '_' . $user . '_' . $id . '.pdf';
    }
}
