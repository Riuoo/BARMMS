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
                  ->orWhereHas('resident', function($uq) use ($search) {
                      $uq->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"]);
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        // Filter by purok
        if ($request->filled('purok')) {
            $purok = $request->get('purok');
            $query->whereHas('resident', function($q) use ($purok) {
                $q->where('address', 'like', "%{$purok}%");
            });
        }
        $documentRequests = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'completed')")->orderByDesc('created_at')->paginate(10);
        return view('admin.requests.document-requests', compact('documentRequests', 'totalRequests', 'pendingCount', 'approvedCount', 'completedCount'));
    }

    public function getDetails($id)
    {
        try {
            $documentRequest = DocumentRequest::with('resident')->findOrFail($id);
            
            return response()->json([
                'user_name' => $documentRequest->resident->full_name ?? 'N/A',
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
            '{{user_name}}' => $documentRequest->resident ? $documentRequest->resident->full_name : '',
            '{{document_type}}' => $documentRequest->document_type,
            '{{purpose}}' => $documentRequest->description,
            '{{admin_name}}' => $adminUser ? $adminUser->full_name : '',
        ];
        return strtr($html, $replacements);
    }

    public function approve($id)
    {
        try {
            $start = microtime(true);
            Log::info('Approve: Start', ['id' => $id]);
            $documentRequest = DocumentRequest::with('resident')->findOrFail($id);
            $user = $documentRequest->resident;
            Log::info('Approve: Fetched request and resident', ['elapsed' => microtime(true) - $start]);
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
            Log::info('Approve: Admin user fetched', ['elapsed' => microtime(true) - $start]);

            // Prefer FK to template, fallback to case-insensitive document_type match
            $template = $documentRequest->documentTemplate
                ?? DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($documentRequest->document_type))])->first();

            if (!$template) {
                notify()->error('No template found for this document type.');
                return redirect()->back();
            }
            Log::info('Approve: Template fetched', ['elapsed' => microtime(true) - $start]);

            // Extract purok number from address if available
            $purokNumber = '';
            if ($documentRequest->resident && $documentRequest->resident->address) {
                if (preg_match('/Purok\s*(\d+)/i', $documentRequest->resident->address, $matches)) {
                    $purokNumber = $matches[1];
                }
            }

            // Format birth date for display (e.g., "June 21, 1967")
            $birthDateFormatted = '';
            if ($documentRequest->resident && $documentRequest->resident->birth_date) {
                try {
                    // Ensure birth_date is a Carbon instance and format it
                    $birthDate = $documentRequest->resident->birth_date;
                    if ($birthDate instanceof \Carbon\Carbon) {
                        $birthDateFormatted = $birthDate->format('F j, Y');
                    } elseif (is_string($birthDate)) {
                        // If it's a string, try to parse it
                        $birthDateFormatted = \Carbon\Carbon::parse($birthDate)->format('F j, Y');
                    }
                } catch (\Exception $e) {
                    // If there's an issue with date formatting, log it and use empty string
                    Log::warning('Birth date formatting failed for resident ID: ' . $documentRequest->resident->id . ' - ' . $e->getMessage());
                    $birthDateFormatted = '';
                }
            } else {
                // Log when birth_date is missing to help with debugging
                if ($documentRequest->resident) {
                    Log::info('Birth date missing for resident: ' . $documentRequest->resident->full_name . ' (ID: ' . $documentRequest->resident->id . ')');
                }
            }

            // Prepare values for placeholders
            $values = [
                'resident_name' => $documentRequest->resident ? $documentRequest->resident->full_name : '',
                'resident_address' => $documentRequest->resident ? $documentRequest->resident->address : '',
                'birth_date' => $birthDateFormatted ?: 'NOT PROVIDED', // Temporary: Show placeholder when missing
                'civil_status' => $documentRequest->resident ? ($documentRequest->resident->marital_status ?? $documentRequest->resident->civil_status ?? '') : '',
                'status' => $documentRequest->resident ? strtolower($documentRequest->resident->marital_status ?? '') : '',
                'purok_number' => $purokNumber,
                'purpose' => $documentRequest->description,
                'day' => date('jS'),
                'month' => date('F'),
                'year' => date('Y'),
                'barangay_name' => $adminUser ? $adminUser->barangay_name : '',
                'municipality_name' => $adminUser ? $adminUser->municipality_name : '',
                'province_name' => $adminUser ? $adminUser->province_name : '',
                'official_name' => $adminUser ? $adminUser->full_name : '',
                'official_position' => $adminUser ? ($adminUser->position ?? '') : '',
            ];

            // Add prepared by (current admin) and captain information for dual-signature footer
            $officials = $this->getBarangayOfficials($adminUser);
            $values = array_merge($values, $officials);

            // Merge dynamic template fields from additional_data
            if (is_array($documentRequest->additional_data)) {
                // Preserve the formatted birth_date from resident record
                $preservedBirthDate = $values['birth_date'];
                
                $values = array_merge($values, $documentRequest->additional_data);
                
                // Restore the formatted birth_date if it was overwritten
                if ($preservedBirthDate && $preservedBirthDate !== 'NOT PROVIDED') {
                    $values['birth_date'] = $preservedBirthDate;
                }
            }

            // Generate the HTML using the template's generateHtml method
            $html = $template->generateHtml($values);
            Log::info('Approve: HTML generated', ['elapsed' => microtime(true) - $start]);

            // Generate the PDF with optimized settings
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'Arial',
                'dpi' => 150, // Lower DPI for faster generation
                'debugCss' => false,
                'debugLayout' => false,
            ]);
            Log::info('Approve: PDF generated', ['elapsed' => microtime(true) - $start]);

            $documentRequest->status = 'approved';
            $documentRequest->resident_is_read = false;
            $documentRequest->save();
            Log::info('Approve: Document request saved', ['elapsed' => microtime(true) - $start]);

            // Send email synchronously for now to ensure it works
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->queue(new DocumentReadyForPickupMail($user->full_name, $documentRequest->document_type));
                    Log::info('Approve: Email queued for background processing', ['elapsed' => microtime(true) - $start]);
                } catch (\Exception $e) {
                    Log::error('Failed to queue DocumentReadyForPickupMail: ' . $e->getMessage());
                    // Don't fail the approval if email fails
                }
            }

            $filename = $this->generateFilename($documentRequest);
            Log::info('Approve: Returning PDF download', ['elapsed' => microtime(true) - $start]);
            notify()->success('Document request approved successfully!');
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

            // Extract purok number from address if available
            $purokNumber = '';
            if ($documentRequest->resident && $documentRequest->resident->address) {
                if (preg_match('/Purok\s*(\d+)/i', $documentRequest->resident->address, $matches)) {
                    $purokNumber = $matches[1];
                }
            }

            // Format birth date for display (e.g., "June 21, 1967")
            $birthDateFormatted = '';
            
            \Log::info('Birth Date Processing Debug', [
                'has_resident' => $documentRequest->resident ? 'YES' : 'NO',
                'resident_id' => $documentRequest->resident ? $documentRequest->resident->id : 'NO_RESIDENT',
                'resident_name' => $documentRequest->resident ? $documentRequest->resident->full_name : 'NO_RESIDENT',
                'birth_date_raw' => $documentRequest->resident ? $documentRequest->resident->birth_date : 'NO_RESIDENT',
                'birth_date_type' => $documentRequest->resident && $documentRequest->resident->birth_date ? gettype($documentRequest->resident->birth_date) : 'NO_BIRTH_DATE'
            ]);
            
            if ($documentRequest->resident && $documentRequest->resident->birth_date) {
                try {
                    // Ensure birth_date is a Carbon instance and format it
                    $birthDate = $documentRequest->resident->birth_date;
                    if ($birthDate instanceof \Carbon\Carbon) {
                        $birthDateFormatted = $birthDate->format('F j, Y');
                        \Log::info('Birth date formatted successfully: ' . $birthDateFormatted);
                    } elseif (is_string($birthDate)) {
                        // If it's a string, try to parse it
                        $birthDateFormatted = \Carbon\Carbon::parse($birthDate)->format('F j, Y');
                        \Log::info('Birth date parsed and formatted: ' . $birthDateFormatted);
                    }
                } catch (\Exception $e) {
                    // If there's an issue with date formatting, log it and use empty string
                    \Log::warning('Birth date formatting failed for resident ID: ' . $documentRequest->resident->id . ' - ' . $e->getMessage());
                    $birthDateFormatted = '';
                }
            } else {
                // Log when birth_date is missing to help with debugging
                if ($documentRequest->resident) {
                    \Log::warning('Birth date is NULL or empty for resident: ' . $documentRequest->resident->full_name . ' (ID: ' . $documentRequest->resident->id . ')');
                } else {
                    \Log::error('No resident found for document request');
                }
            }

            // Prepare values for placeholders
            $values = [
                'resident_name' => $documentRequest->resident ? $documentRequest->resident->full_name : '',
                'resident_address' => $documentRequest->resident ? $documentRequest->resident->address : '',
                'birth_date' => $birthDateFormatted ?: 'NOT PROVIDED', // Temporary: Show placeholder when missing
                'civil_status' => $documentRequest->resident ? ($documentRequest->resident->marital_status ?? $documentRequest->resident->civil_status ?? '') : '',
                'status' => $documentRequest->resident ? strtolower($documentRequest->resident->marital_status ?? '') : '',
                'purok_number' => $purokNumber,
                'purpose' => $documentRequest->description,
                'day' => date('jS'),
                'month' => date('F'),
                'year' => date('Y'),
                'barangay_name' => $adminUser ? $adminUser->barangay_name : '',
                'municipality_name' => $adminUser ? $adminUser->municipality_name : '',
                'province_name' => $adminUser ? $adminUser->province_name : '',
                'official_name' => $adminUser ? $adminUser->full_name : '',
                'official_position' => $adminUser ? ($adminUser->position ?? '') : '',
            ];

            // Add prepared by (current admin) and captain information for dual-signature footer
            $officials = $this->getBarangayOfficials($adminUser);
            $values = array_merge($values, $officials);

            // Merge dynamic template fields from additional_data
            if (is_array($documentRequest->additional_data)) {
                \Log::info('Additional data being merged:', $documentRequest->additional_data);
                
                // Preserve the formatted birth_date from resident record
                $preservedBirthDate = $values['birth_date'];
                
                // Check if additional_data contains birth_date that might overwrite our formatted one
                if (isset($documentRequest->additional_data['birth_date'])) {
                    \Log::warning('additional_data contains birth_date - will preserve formatted version!', [
                        'formatted_birth_date' => $values['birth_date'],
                        'additional_data_birth_date' => $documentRequest->additional_data['birth_date']
                    ]);
                }
                
                $values = array_merge($values, $documentRequest->additional_data);
                
                // Restore the formatted birth_date if it was overwritten
                if ($preservedBirthDate && $preservedBirthDate !== 'NOT PROVIDED') {
                    $values['birth_date'] = $preservedBirthDate;
                    \Log::info('Restored formatted birth_date: ' . $preservedBirthDate);
                }
                
                \Log::info('Birth date after merging additional_data:', ['birth_date' => $values['birth_date']]);
            }

            // Debug: Log final values being passed to template
            \Log::info('Final values passed to generateHtml:', [
                'birth_date' => $values['birth_date'] ?? 'NOT_SET',
                'resident_name' => $values['resident_name'] ?? 'NOT_SET',
                'all_keys' => array_keys($values)
            ]);

            $html = $template->generateHtml($values);

            // Generate PDF with optimized settings for speed
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'Arial',
                'dpi' => 150, // Lower DPI for faster generation
                'debugCss' => false,
                'debugLayout' => false,
            ]);

            if ($documentRequest->status === 'approved') {
                $documentRequest->status = 'completed';
                $documentRequest->save();
            }

            $filename = $this->generateFilename($documentRequest);
            notify()->success('PDF generated successfully!');
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating document request PDF: ' . $e->getMessage());
            notify()->error('Failed to generate PDF: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function markAsComplete(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            $user = $documentRequest->resident;
            if (!$user) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'This resident record no longer exists.'], 422);
                }
                notify()->error('This resident record no longer exists.');
                return back();
            }
            if ($user->active === false) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'This user account is inactive and cannot make transactions.'], 422);
                }
                notify()->error('This user account is inactive and cannot make transactions.');
                return back();
            }
            if ($documentRequest->status !== 'approved') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Only approved requests can be marked as completed.'], 422);
                }
                notify()->error('Only approved requests can be marked as completed.');
                return back();
            }
            $documentRequest->status = 'completed';
            $documentRequest->save();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            notify()->success('Document request marked as completed.');
            return back();
        } catch (\Exception $e) {
            Log::error('Error marking document request as completed: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to mark as completed: ' . $e->getMessage()], 500);
            }
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
            'template_fields' => 'nullable|array',
            'template_fields.*' => 'nullable|string|max:1000',
            'privacy_consent' => 'required|accepted',
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
        // Prevent multiple ongoing requests (pending/processing/approved)
        if (DocumentRequest::where('resident_id', $validated['resident_id'])
                ->whereIn('status', ['pending', 'processing', 'approved'])
                ->exists()) {
            return response()->json([
                'error' => 'Resident already has an ongoing document request. Complete it before creating a new one.'
            ], 422);
        }

        try {
            $template = null;
            if (!empty($validated['document_template_id'])) {
                $template = DocumentTemplate::find($validated['document_template_id']);
            } else {
                $template = DocumentTemplate::whereRaw('LOWER(document_type) = ?', [strtolower(trim($validated['document_type']))])->first();
            }

            // Prepare additional_data from template_fields
            $additionalData = [];
            if (!empty($validated['template_fields'])) {
                // Filter out empty values and clean the array
                $additionalData = array_filter($validated['template_fields'], function($value) {
                    return $value !== null && $value !== '';
                });
            }

            $documentRequest = DocumentRequest::create([
                'resident_id' => $validated['resident_id'],
                'document_type' => $template?->document_type ?? $validated['document_type'],
                'document_template_id' => $template?->id,
                'description' => $validated['description'],
                'additional_data' => !empty($additionalData) ? $additionalData : null,
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
            'resident_name' => $documentRequest->resident?->full_name ?? '',
            'resident_address' => $documentRequest->resident?->address ?? '',
            'civil_status' => $documentRequest->resident ? ($documentRequest->resident->marital_status ?? $documentRequest->resident->civil_status ?? '') : '',
            'purpose' => $documentRequest->description,
            'day' => date('jS'),
            'month' => date('F'),
            'year' => date('Y'),
            'barangay_name' => $adminUser->barangay_name ?? '',
            'municipality_name' => $adminUser->municipality_name ?? '',
            'province_name' => $adminUser->province_name ?? '',
            'official_name' => $adminUser->full_name ?? '',
            'official_position' => $adminUser->position ?? '',
        ];

        // Add prepared by (current admin) and captain information for dual-signature footer
        $officials = $this->getBarangayOfficials($adminUser);
        $values = array_merge($values, $officials);

        // Merge dynamic template fields from additional_data
        if (is_array($documentRequest->additional_data)) {
            $values = array_merge($values, $documentRequest->additional_data);
        }

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
        $name = $documentRequest->resident ? $documentRequest->resident->full_name : 'unknown';
        $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $name); // Remove special chars
        $name = strtolower(str_replace(' ', '_', $name));
        $date = date('Y-m-d');
        return "document_request_{$name}_{$date}.pdf";
    }

    public function checkActive($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        $user = $documentRequest->resident;
        return response()->json(['active' => $user && $user->active ? true : false]);
    }

    /**
     * Get barangay officials for document signatures
     * Returns: prepared_by_name (secretary or current admin user) and captain_name (Punong Barangay)
     */
    protected function getBarangayOfficials($adminUser = null)
    {
        // Get secretary name (who prepared the document)
        // If no secretary found, fall back to current admin user
        $secretary = BarangayProfile::where('role', 'secretary')
            ->where('active', true)
            ->first();
        
        $preparedByName = $secretary ? $secretary->full_name : ($adminUser ? $adminUser->full_name : '');
        
        // Get current Punong Barangay (captain)
        $captain = BarangayProfile::where('role', 'captain')
            ->where('active', true)
            ->first();
        
        return [
            'prepared_by_name' => $preparedByName,
            'captain_name' => $captain ? $captain->full_name : '',
        ];
    }
}
