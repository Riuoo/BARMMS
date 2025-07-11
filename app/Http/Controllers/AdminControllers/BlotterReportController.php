<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\BlotterRequest;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Residents;
use Illuminate\Http\Request;

class BlotterReportController
{
    public function blotterReport()
    {
        $blotterRequests = BlotterRequest::with('user')->get();
        return view('admin.blotter-reports', compact('blotterRequests'));
    }

    public function getDetails($id)
    {
        try {
            $blotterRequest = BlotterRequest::with('user')->findOrFail($id);
            
            return response()->json([
                'user_name' => $blotterRequest->user->name ?? 'N/A',
                'recipient_name' => $blotterRequest->recipient_name,
                'description' => $blotterRequest->description,
                'status' => $blotterRequest->status,
                'created_at' => $blotterRequest->created_at->format('M d, Y \a\t g:i A'),
                'media_files' => $blotterRequest->media ? [
                    [
                        'name' => 'Attached File',
                        'url' => asset('storage/' . $blotterRequest->media)
                    ]
                ] : null,
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

            if ($blotter->status === 'approved') {
                notify()->error('This blotter report has already been approved.');
                return redirect()->back();
            }

            $blotter->status = 'approved';
            $blotter->approved_at = now();
            $blotter->summon_date = $validated['hearing_date']; // Save the hearing_date as summon_date
            $blotter->attempts++;
            $blotter->save();

            Log::info('Blotter report approved successfully', ['blotter_id' => $blotter->id]);

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
            }
            
            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdfs.summons_pdf', [
                'blotter' => $blotter,
                'adminUser' => $adminUser
            ]);
            return $pdf->download("summon_notice_{$blotter->id}.pdf");
        } catch (\Exception $e) {
            Log::error("Error approving blotter report: " . $e->getMessage());
            notify()->error('Failed to approve blotter report: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function markAsComplete($id)
    {
        try {
            $blotter = BlotterRequest::findOrFail($id);
            
            if ($blotter->status !== 'approved') {
                notify()->error('Only approved reports can be marked as complete');
                return back();
            
            }

            $blotter->status = 'completed';
            $blotter->completed_at = now();
            $blotter->save();

            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
            }
            
            // Generate final resolution PDF
            $pdf = Pdf::loadView('admin.pdfs.resolution_pdf', [
                'blotter' => $blotter,
                'adminUser' => $adminUser
            ]);
            $filename = "case_resolution_{$blotter->id}.pdf";
            
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
        $residents = Residents::all();
        return view('admin.create_blotter_report', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'recipient_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'summon_date' => 'required|date|after:today'
        ]);
        try {
            $blotter = new BlotterRequest();
            $blotter->user_id = $validated['resident_id'];
            $blotter->recipient_name = $validated['recipient_name'];
            $blotter->type = $validated['type'];
            $blotter->description = $validated['description'];
            $blotter->status = 'approved';
            $blotter->approved_at = now();
            $blotter->summon_date = $validated['summon_date'];
            $blotter->attempts = 1;
            if ($request->hasFile('media')) {
                $blotter->media = $request->file('media')->store('blotter_media', 'public');
            }
            $blotter->save();
            // Get admin user data from session
            $adminUser = null;
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
            }
            
            // Generate the PDF immediately after saving
            $pdf = Pdf::loadView('admin.pdfs.summons_pdf', [
                'blotter' => $blotter,
                'adminUser' => $adminUser
            ]);
            $filename = "summon_notice_{$blotter->id}.pdf";
            
            return $pdf->download($filename); // Download the PDF
        } catch (\Exception $e) {
            notify()->error('Error creating blotter: ' . $e->getMessage());
            return back();
            
        }
    }

    public function generateNewSummons(Request $request, $id)
    {
        $blotter = BlotterRequest::findOrFail($id);
        if ($blotter->attempts >= 3) {
            notify()->error('Maximum attempts reached for generating new summons.');
            return back();
            
        }
        // Increment the attempts
        $blotter->attempts++;
        $blotter->summon_date = $request->input('new_summon_date'); // Get new date from request
        $blotter->save();
        // Get admin user data from session
        $adminUser = null;
        if (session()->has('user_role') && session('user_role') === 'barangay') {
            $adminUser = \App\Models\BarangayProfile::find(session('user_id'));
        }
        
        // Generate the new summons PDF
        $pdf = Pdf::loadView('admin.pdfs.summons_pdf', [
            'blotter' => $blotter,
            'adminUser' => $adminUser
        ]);
        $filename = "new_summon_notice_{$blotter->id}.pdf";
        return $pdf->download($filename); // Download the new summons PDF
    }

    public function searchResidents(Request $request)
    {
        $term = $request->query('term');
        $page = $request->query('page', 1);
        
        return Residents::query()
            ->where('name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->paginate(10, ['id', 'name', 'email'], 'page', $page)
            ->map(function($resident) {
                return [
                    'id' => $resident->id,
                    'text' => $resident->name,
                    'name' => $resident->name,
                    'email' => $resident->email
                ];
            });
    }

}