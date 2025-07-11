<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Residents;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ResidentController2
{
    /**
     * Show the resident dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        
    $userId = Session::get('user_id');
    $resident = Residents::find($userId);

    if (!$resident) {
        return redirect()->route('landing');
    }

    return view('resident.dashboard', compact('resident'));
    }

    /**
     * Show the form for creating a new blotter report.
     *
     * @return \Illuminate\View\View
     */
    public function requestBlotter()
    {
        return view('resident.request_blotter_report');
    }

    /**
     * Store a newly requested blotter report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBlotter(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        // Get user ID from session
        $userId = Session::get('user_id');
        if (!$userId) {
            \Log::warning('Resident user ID not found in session for blotter submission.');
            notify()->error('You must be logged in to submit a report.');
            return redirect()->route('landing');
        }
        $blotter = new BlotterRequest();
        $blotter->user_id = $userId; // Set the user ID
        $blotter->recipient_name = $request->recipient_name;
        $blotter->type = $request->type;
        $blotter->description = $request->description;
        $blotter->status = 'pending'; // Default status for residents
        $blotter->attempts = 0; // Assuming this is the first attempt
        if ($request->hasFile('media')) {
            $blotter->media = $request->file('media')->store('blotter_media', 'public');
        }
        try {
            $blotter->save(); // Save the blotter report to the database
            notify()->success('Blotter report submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            \Log::error("Error creating blotter report: " . $e->getMessage());
            notify()->error('Error creating blotter: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Show the form for creating a new document request.
     *
     * @return \Illuminate\View\View
     */
    public function requestDocument()
    {
        return view('resident.request_document_request');
    }

    /**
     * Show the form for creating a new document request.
     *
     * @return \Illuminate\View\View
     */
    public function storeDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        // Get user ID from session
        $userId = Session::get('user_id');
        if (!$userId) {
            \Log::warning('Resident user ID not found in session for document submission.');
            notify()->error('You must be logged in to submit a document request.');
            return redirect()->route('landing');
        }
        $documentRequest = new DocumentRequest();
        $documentRequest->user_id = $userId; // Set the user ID
        $documentRequest->document_type = $request->document_type;
        $documentRequest->description = $request->description;
        $documentRequest->status = 'pending'; // Default status for residents
        try {
            $documentRequest->save(); // Save the document request to the database
            notify()->success('Document request submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            \Log::error("Error creating document request: " . $e->getMessage());
            notify()->error('Error creating document request: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Display a listing of the resident's blotter and document requests.
     *
     * @return \Illuminate\View\View
     */
    public function myRequests()
    {
        $userId = Session::get('user_id');
        $blotterRequests = BlotterRequest::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        $documentRequests = DocumentRequest::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return view('resident.my_requests', compact('blotterRequests', 'documentRequests'));
    }
    /**
     * Show the health status reporting page (Recommendation).
     *
     * @return \Illuminate\View\View
     */
    public function healthStatus()
    {
        // You would implement logic here to display or submit health concerns
        return view('resident.health_status');
    }

    /**
     * Show the announcements page (Recommendation).
     *
     * @return \Illuminate\View\View
     */
    public function announcements()
    {
        // You would fetch and display announcements here
        return view('resident.announcements');
    }
}