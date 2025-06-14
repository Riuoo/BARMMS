<?php

namespace App\Http\Controllers;

use App\Models\BlotterRequest;
use Illuminate\Support\Facades\Log;

class BlotterReportController extends Controller
{
    /**
     * Display the list of blotter reports.
     */
    public function index()
    {
        $blotterRequests = BlotterRequest::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.blotter-reports', compact('blotterRequests'));
    }

    /**
     * Approve a blotter report.
     */
    public function approve($id)
    {
        try {
            $blotterReport = BlotterRequest::findOrFail($id);
            
            if ($blotterReport->status !== 'pending') {
                return redirect()->back()->with('error', 'Blotter report already processed.');
            }
            $blotterReport->status = 'approved';
            $blotterReport->save();
            return redirect()->back()->with('success', 'Blotter report approved successfully.');
        } catch (\Exception $e) {
            Log::error('Error approving blotter report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve blotter report.');
        }
    }
}

