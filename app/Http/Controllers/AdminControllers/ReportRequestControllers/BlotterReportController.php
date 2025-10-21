<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\BlotterRequest;
use App\Models\BarangayProfile;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Residents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BlotterSummonReadyMail;

class BlotterReportController
{
    public function blotterReport(Request $request)
    {
        // Statistics from full dataset
        $totalReports = BlotterRequest::count();
        $pendingCount = BlotterRequest::where('status', 'pending')->count();
        $approvedCount = BlotterRequest::where('status', 'approved')->count();
        $completedCount = BlotterRequest::where('status', 'completed')->count();

        // For display (filtered)
        $query = BlotterRequest::with('resident');
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('resident', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $blotterRequests = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'completed')")->orderByDesc('created_at')->paginate(10);
        return view('admin.requests.blotter-reports', compact('blotterRequests', 'totalReports', 'pendingCount', 'approvedCount', 'completedCount'));
    }

    public function getDetails($id)
    {
        try {
            $blotterRequest = BlotterRequest::with('resident')->findOrFail($id);
            
            // Prepare media files for response
            $mediaFiles = null;
            if ($blotterRequest->media) {
                $mediaFiles = [];
                foreach ($blotterRequest->media as $file) {
                    $mediaFiles[] = [
                        'name' => $file['name'] ?? 'Attached File',
                        'url' => asset('storage/' . $file['path']),
                        'type' => $file['type'] ?? 'unknown',
                        'size' => $file['size'] ?? 0,
                    ];
                }
            }
            
            return response()->json([
                'user_name' => $blotterRequest->resident->name ?? 'N/A',
                'respondent_name' => $blotterRequest->resident ? $blotterRequest->resident->name : $blotterRequest->recipient_name,
                'description' => $blotterRequest->description,
                'status' => $blotterRequest->status,
                'created_at' => $blotterRequest->created_at->format('M d, Y \a\t g:i A'),
                'media_files' => $mediaFiles,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching blotter request details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch details'], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        Log::info('Approve method called for blotter ID: ' . $id);

        // Accept the form field 'hearing_date' and save as 'summon_date'
        $validated = $request->validate([
            'hearing_date' => 'required|date|after:today'
        ]);

        try {
            $blotter = BlotterRequest::findOrFail($id);
            $user = $blotter->resident;
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return redirect()->back();
            }
            if ($user->active === false) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return redirect()->back();
            }

            if ($blotter->status === 'approved') {
                notify()->error('This blotter report has already been approved.');
                return redirect()->back();
            }

            DB::beginTransaction();

            $blotter->status = 'approved';
            $blotter->approved_at = now();
            $blotter->summon_date = $validated['hearing_date']; // Save the hearing_date as summon_date
            $blotter->attempts++;
            $blotter->is_read = false; // Set to false so it appears as unread notification
            $blotter->resident_is_read = false; // Set to false so resident sees it as unread
            $blotter->save();

            // Send email to resident if email exists
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->queue(new BlotterSummonReadyMail(
                        $user->name,
                        $blotter->recipient_name ?? $blotter->type,
                        $blotter->summon_date ? $blotter->summon_date->format('F d, Y h:i A') : 'N/A'
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to queue BlotterSummonReadyMail: ' . $e->getMessage());
                }
            }

            notify()->success('Blotter report approved successfully.');

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = BarangayProfile::find(session('user_id'));
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.pdfs.summons_pdf', [
                'blotter' => $blotter,
                'adminUser' => $adminUser
            ]);
            $filename = $this->generateFilename($blotter, 'summon_notice');
            $response = $pdf->download($filename);

            DB::commit();
            return $response;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error approving blotter report: " . $e->getMessage());
            // Return JSON so AJAX handler can surface a clear error
            return response()->json([
                'error' => 'Failed to approve blotter report: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function markAsComplete($id)
    {
        try {
            $blotter = BlotterRequest::findOrFail($id);
            $user = $blotter->resident;
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return back();
            }
            if ($user->active === false) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return back();
            }
            
            if ($blotter->status !== 'approved') {
                notify()->error('Only approved reports can be marked as complete');
                return back();
            
            }

            $blotter->status = 'completed';
            $blotter->completed_at = now();
            $blotter->save();

            notify()->success('Blotter report marked as completed.');

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = BarangayProfile::find(session('user_id'));
            }
            
            // Generate final resolution PDF
            $pdf = Pdf::loadView('admin.pdfs.resolution_pdf', [
                'blotter' => $blotter,
                'adminUser' => $adminUser
            ]);
            $filename = $this->generateFilename($blotter, 'case_resolution');
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("Blotter completion failed: " . $e->getMessage());
            notify()->error('Failed to complete blotter: ' . $e->getMessage());
            return back();
            
        }
    }

    // NEW METHOD: Generate PDF for a blotter report
    // public function generatePdf($id)
    // {
    //     try {
    //         $blotterReport = BlotterRequest::with('user')->findOrFail($id);

    //         // Ensure the report is approved before generating a formal document
    //         if ($blotterReport->status !== 'approved') {
    //             return redirect()->back()->with('error', 'Only approved blotter reports can be generated as PDF.');
    //         }

    //         $pdf = Pdf::loadView('admin.pdfs.blotter_report_pdf', compact('blotterReport'));
    //         return $pdf->download('blotter_report_' . $blotterReport->id . '.pdf');

    //     } catch (\Exception $e) {
    //         Log::error('Error generating blotter report PDF: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Failed to generate blotter report PDF: ' . $e->getMessage());
    //     }
    // }

    public function create()
    {
        $residents = Residents::where('active', true)->get();
        return view('admin.requests.create_blotter_report', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complainant_name' => 'required|string|max:255',
            'resident_id' => 'nullable|exists:residents,id',
            'recipient_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4,avi,mov,wmv|max:10240',
            'summon_date' => 'required|date|after:today'
        ]);
        // Only validate user if resident_id is provided
        if ($validated['resident_id']) {
            $user = Residents::find($validated['resident_id']);
            if (!$user || !$user->active) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return back()->withInput();
            }
            // Prevent multiple ongoing blotter requests
            if (BlotterRequest::where('resident_id', $validated['resident_id'])
                    ->whereIn('status', ['pending', 'processing', 'approved'])
                    ->exists()) {
                notify()->error('Resident already has an ongoing blotter request. Complete it before creating a new one.');
                return back()->withInput();
            }
        }
        try {
            $blotter = new BlotterRequest();
            $blotter->complainant_name = $validated['complainant_name'];
            $blotter->resident_id = $validated['resident_id']; // This is now the respondent
            $blotter->recipient_name = $validated['recipient_name'];
            $blotter->type = $validated['type'];
            $blotter->description = $validated['description'];
            $blotter->status = 'approved';
            $blotter->approved_at = now();
            $blotter->summon_date = $validated['summon_date'];
            $blotter->attempts = 1;
            
            // Handle multiple file uploads
            if ($request->hasFile('media')) {
                $mediaFiles = [];
                foreach ($request->file('media') as $file) {
                    $path = $file->store('blotter_media', 'public');
                    $mediaFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ];
                }
                $blotter->media = $mediaFiles;
            }
            
            $blotter->save();
            
            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = BarangayProfile::find(session('user_id'));
            }
            
            // Generate the PDF immediately after saving
            $pdf = Pdf::loadView('admin.pdfs.summons_pdf', [
                'blotter' => $blotter,
                'adminUser' => $adminUser
            ]);
            $filename = $this->generateFilename($blotter, 'blotter_report');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            notify()->error('Error creating blotter: ' . $e->getMessage());
            return back();
        }
    }

    public function generateNewSummons(Request $request, $id)
    {
        $blotter = BlotterRequest::findOrFail($id);
        $user = $blotter->resident;
        if (!$user) {
            notify()->error('This resident record no longer exists.');
            return back();
        }
        if ($user->active === false) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back();
        }
        if ($blotter->attempts >= 3) {
            notify()->error('Maximum attempts reached for generating new summons.');
            return back();
            
        }
        // Validate new date must be strictly after current summon_date (or approved_at if no summon yet)
        $request->validate([
            'new_summon_date' => ['required', 'date', function ($attr, $value, $fail) use ($blotter) {
                $baseDate = $blotter->summon_date ?? $blotter->approved_at ?? now();
                if ($baseDate && strtotime($value) <= strtotime($baseDate)) {
                    $fail('The new summon date must be after the previous summon date.');
                }
            }],
        ]);

        // Increment the attempts
        $blotter->attempts++;
        $blotter->summon_date = $request->input('new_summon_date');
        $blotter->save();

        notify()->success('New summon generated successfully.');

        // Get admin user data from session
        $adminUser = null;
        if (session()->has('user_role') && session('user_role') === 'barangay') {
            $adminUser = BarangayProfile::find(session('user_id'));
        }
        
        // Generate the new summons PDF
        $pdf = Pdf::loadView('admin.pdfs.summons_pdf', [
            'blotter' => $blotter,
            'adminUser' => $adminUser
        ]);
        $filename = $this->generateFilename($blotter, 'new_summon_notice');
        return $pdf->download($filename); // Download the new summons PDF
    }

    // Add this method to generate a filename for the PDF
    protected function generateFilename($blotterRequest, $type = 'blotter_report')
    {
        $name = $blotterRequest->resident ? $blotterRequest->resident->name : 'unknown';
        $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $name); // Remove special chars
        $name = strtolower(str_replace(' ', '_', $name));
        $date = date('Y-m-d');
        return "{$type}_{$name}_{$date}.pdf";
    }
}