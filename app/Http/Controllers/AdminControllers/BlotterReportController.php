<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\BlotterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BlotterReportController
{
    public function blotterReport()
    {
    $blotterRequests = BlotterRequest::with('user')->get();
    return view('admin.blotter-reports', compact('blotterRequests'));
    }

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

