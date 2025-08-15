<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentReadyForPickupMail;
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
        $query = DocumentRequest::with('resident');
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('document_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('resident', function($uq) use ($search) {
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
            $documentRequest = DocumentRequest::with('resident')->findOrFail($id);
            
            return response()->json([
                'user_name' => $documentRequest->resident->name ?? 'N/A',
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
            '{{user_name}}' => $documentRequest->resident ? $documentRequest->resident->name : '',
            '{{document_type}}' => $documentRequest->document_type,
            '{{purpose}}' => $documentRequest->description,
            '{{admin_name}}' => $adminUser ? $adminUser->name : '',
        ];
        return strtr($html, $replacements);
    }

    public function approve($id)
    {
        try {
            $documentRequest = DocumentRequest::with('resident')->findOrFail($id);
            $user = $documentRequest->resident;
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return redirect()->back();
            }
            if ($user->active === false) {
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

            // Prefer FK to template, fallback to case-insensitive document_type match
            $template = $documentRequest->documentTemplate
                ?? DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($documentRequest->document_type))])->first();

            if (!$template) {
                notify()->error('No template found for this document type.');
                return redirect()->back();
            }

            // Prepare values for placeholders
            $values = [
                'resident_name' => $documentRequest->resident ? $documentRequest->resident->name : '',
                'resident_address' => $documentRequest->resident ? $documentRequest->resident->address : '',
                'civil_status' => $documentRequest->resident ? $documentRequest->resident->civil_status : '',
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

            $documentRequest->status = 'approved';
            $documentRequest->resident_is_read = false;
            $documentRequest->save();

            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new DocumentReadyForPickupMail($user->name, $documentRequest->document_type));
                } catch (\Exception $e) {
                    Log::error('Failed sending DocumentReadyForPickupMail: ' . $e->getMessage());
                }
            }

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
            $documentRequest = DocumentRequest::with('resident')->findOrFail($id);
            $user = $documentRequest->resident;
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return redirect()->back();
            }
            if ($user->active === false) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return redirect()->back();
            }

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = BarangayProfile::find(session('user_id'));
            }

            // Prefer FK to template, fallback to case-insensitive document_type match
            $template = $documentRequest->documentTemplate
                ?? DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($documentRequest->document_type))])->first();

            if (!$template) {
                notify()->error('No template found for this document type.');
                return redirect()->back();
            }

            // Prepare values for placeholders
            $values = [
                'resident_name' => $documentRequest->resident ? $documentRequest->resident->name : '',
                'resident_address' => $documentRequest->resident ? $documentRequest->resident->address : '',
                'civil_status' => $documentRequest->resident ? $documentRequest->resident->civil_status : '',
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

            $html = $template->generateHtml($values);

            $pdf = Pdf::loadHTML($html);

            if ($documentRequest->status === 'approved') {
                $documentRequest->status = 'completed';
                $documentRequest->save();
            }

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
            $user = $documentRequest->resident;
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return back();
            }
            if ($user->active === false) {
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
        $templates = DocumentTemplate::where('is_active', true)->orderBy('document_type')->get();
        return view('admin.requests.create_document_request', compact('residents', 'templates'));
    }

    // Store + return request id
    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
            'document_template_id' => 'nullable|exists:document_templates,id',
        ]);

        $user = Residents::find($validated['resident_id']);
        if (!$user) {
            return response()->json([
                'error' => 'Selected resident does not exist.'
            ], 422);
        }
        if ($user->active === false) {
            return response()->json([
                'error' => 'This user account is inactive and cannot make transactions.'
            ], 422);
        }

        try {
            $template = null;
            if (!empty($validated['document_template_id'])) {
                $template = DocumentTemplate::find($validated['document_template_id']);
            } else {
                $template = DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($validated['document_type']))])->first();
            }

            $documentRequest = DocumentRequest::create([
                'resident_id' => $validated['resident_id'],
                'document_type' => $template?->document_type ?? $validated['document_type'],
                'document_template_id' => $template?->id,
                'description' => $validated['description'],
                'status' => 'approved',
            ]);

            return response()->json([
                'success' => true,
                'id' => $documentRequest->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating document request: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error creating document request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Download route
    public function downloadRequest($id)
    {
        $documentRequest = DocumentRequest::with('resident')->findOrFail($id);

        $adminUser = null;
        if (session()->has('user_role') && session('user_role') === 'barangay') {
            $adminUser = BarangayProfile::find(session('user_id'));
        }

        $template = $documentRequest->documentTemplate
            ?? DocumentTemplate::whereRaw(
                'LOWER(document_type) = ?',
                [strtolower(trim($documentRequest->document_type))]
            )->firstOrFail();

        $values = [
            'resident_name' => $documentRequest->resident?->name ?? '',
            'resident_address' => $documentRequest->resident?->address ?? '',
            'civil_status' => $documentRequest->resident?->civil_status ?? '',
            'purpose' => $documentRequest->description,
            'day' => date('jS'),
            'month' => date('F'),
            'year' => date('Y'),
            'barangay_name' => $adminUser->barangay_name ?? '',
            'municipality_name' => $adminUser->municipality_name ?? '',
            'province_name' => $adminUser->province_name ?? '',
            'official_name' => $adminUser->name ?? '',
            'official_position' => $adminUser->position ?? '',
        ];

        $html = $template->generateHtml($values);
        $pdf = Pdf::loadHTML($html);
        $filename = $this->generateFilename($documentRequest);

        return $pdf->download($filename);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'user_id' => 'required|exists:residents,id',
    //         'document_type' => 'required|string|max:255',
    //         'description' => 'required|string',
    //     ]);

    //     $user = Residents::find($validated['user_id']);
    //     if (!$user || !$user->active) {
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'error' => 'This user account is inactive and cannot make transactions.'
    //             ], 422);
    //         }
    //         notify()->error('This user account is inactive and cannot make transactions.');
    //         return back()->withInput();
    //     }

    //     try {
    //         $documentRequest = DocumentRequest::create([
    //             'user_id' => $validated['user_id'],
    //             'document_type' => $validated['document_type'],
    //             'description' => $validated['description'],
    //             'status' => 'approved',
    //         ]);

    //         $adminUser = null;
    //         if (session()->has('user_role') && session('user_role') === 'barangay') {
    //             $adminUser = BarangayProfile::find(session('user_id'));
    //         }

    //         $template = DocumentTemplate::whereRaw(
    //             'LOWER(document_type) = ?',
    //             [strtolower(trim($documentRequest->document_type))]
    //         )->first();

    //         if (!$template) {
    //             if ($request->expectsJson()) {
    //                 return response()->json([
    //                     'error' => 'No template found for this document type.'
    //                 ], 404);
    //             }
    //             notify()->error('No template found for this document type.');
    //             return redirect()->back();
    //         }

    //         $values = [
    //             'resident_name' => $documentRequest->user?->name ?? '',
    //             'resident_address' => $documentRequest->user?->address ?? '',
    //             'civil_status' => $documentRequest->user?->civil_status ?? '',
    //             'purpose' => $documentRequest->description,
    //             'day' => date('jS'),
    //             'month' => date('F'),
    //             'year' => date('Y'),
    //             'barangay_name' => $adminUser->barangay_name ?? '',
    //             'municipality_name' => $adminUser->municipality_name ?? '',
    //             'province_name' => $adminUser->province_name ?? '',
    //             'official_name' => $adminUser->name ?? '',
    //             'official_position' => $adminUser->position ?? '',
    //         ];

    //         $html = $template->generateHtml($values);
    //         $pdf = Pdf::loadHTML($html);
    //         $filename = $this->generateFilename($documentRequest);

    //         return $pdf->download($filename);

    //     } catch (\Exception $e) {
    //         Log::error('Error creating document request: ' . $e->getMessage());

    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'error' => 'Error creating document request: ' . $e->getMessage()
    //             ], 500);
    //         }

    //         notify()->error('Error creating document request: ' . $e->getMessage());
    //         return back()->withInput();
    //     }
    // }

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
        $user = $documentRequest->resident ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', strtolower($documentRequest->resident->name)) : 'unknown_user';
        $id = $documentRequest->id;
        return $type . '_' . $user . '_' . $id . '.pdf';
    }
}
