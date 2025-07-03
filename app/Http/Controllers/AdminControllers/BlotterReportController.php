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

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'summon_date' => 'required|date|after:today'
        ]);

        try {
            $blotter = BlotterRequest::findOrFail($id);

            // Check if attempts exceed 3
            if ($blotter->attempts >= 3) {
                return back()->with('error', 'Maximum attempts reached for generating new summons.');
            }

            $blotter->status = 'approved';
            $blotter->approved_at = now();
            $blotter->summon_date = $validated['summon_date'];
            $blotter->attempts++; // Increment attempts
            $blotter->save();

            // Generate immediate summon PDF
            $pdf = Pdf::loadView('admin.pdfs.summons_pdf', ['blotter' => $blotter]);
            $filename = "summon_notice_{$blotter->id}_{$blotter->user->name}.pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error("Blotter approval failed: " . $e->getMessage());
            return back()->with('error', 'Failed to approve blotter: ' . $e->getMessage());
        }
    }

    public function markAsComplete($id)
    {
        try {
            $blotter = BlotterRequest::findOrFail($id);
            
            if ($blotter->status !== 'approved') {
                return back()->with('error', 'Only approved reports can be marked as complete');
            }

            $blotter->status = 'completed';
            $blotter->completed_at = now();
            $blotter->save();

            // Generate final resolution PDF
            $pdf = Pdf::loadView('admin.pdfs.resolution_pdf', ['blotter' => $blotter]);
            $filename = "case_resolution_{$blotter->id}.pdf";
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("Blotter completion failed: " . $e->getMessage());
            return back()->with('error', 'Failed to complete blotter: ' . $e->getMessage());
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
            // Generate the PDF immediately after saving
            $pdf = Pdf::loadView('admin.pdfs.summons_pdf', ['blotter' => $blotter]);
            $filename = "summon_notice_{$blotter->id}.pdf";
            
            return $pdf->download($filename); // Download the PDF
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating blotter: ' . $e->getMessage());
        }
    }

    public function generateNewSummons(Request $request, $id)
    {
        $blotter = BlotterRequest::findOrFail($id);
        if ($blotter->attempts >= 3) {
            return back()->with('error', 'Maximum attempts reached for generating new summons.');
        }
        // Increment the attempts
        $blotter->attempts++;
        $blotter->summon_date = $request->input('new_summon_date'); // Get new date from request
        $blotter->save();
        // Generate the new summons PDF
        $pdf = Pdf::loadView('admin.pdfs.summons_pdf', ['blotter' => $blotter]);
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