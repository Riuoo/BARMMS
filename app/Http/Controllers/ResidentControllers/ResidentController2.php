<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\HealthStatus;
use Illuminate\Http\Request;
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

    // Fetch the resident's blotter and document requests for dashboard statistics
    $blotterRequests = BlotterRequest::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    $documentRequests = DocumentRequest::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    $healthStatusReports = HealthStatus::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

    return view('resident.dashboard', compact('resident', 'blotterRequests', 'documentRequests', 'healthStatusReports'));
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
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4,avi,mov,wmv|max:10240', // 10MB max per file
        ]);
        
        // Get user ID from session
        $userId = Session::get('user_id');
        if (!$userId) {
            Log::warning('Resident user ID not found in session for blotter submission.');
            notify()->error('You must be logged in to submit a report.');
            return redirect()->route('landing');
        }
        
        $blotter = new BlotterRequest();
        $blotter->user_id = $userId;
        $blotter->recipient_name = $request->recipient_name;
        $blotter->type = $request->type;
        $blotter->description = $request->description;
        $blotter->status = 'pending';
        $blotter->attempts = 0;
        
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
        
        try {
            $blotter->save();
            notify()->success('Blotter report submitted successfully.');
            return redirect()->route('resident.my-requests');
        } catch (\Exception $e) {
            Log::error("Error creating blotter report: " . $e->getMessage());
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
            Log::warning('Resident user ID not found in session for document submission.');
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
            Log::error("Error creating document request: " . $e->getMessage());
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
        $healthStatusRequests = HealthStatus::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return view('resident.my_requests', compact('blotterRequests', 'documentRequests', 'healthStatusRequests'));
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
     * Store the health status report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeHealthStatus(Request $request)
    {
        $request->validate([
            'concern_type' => 'required|string|max:255',
            'severity' => 'required|string|in:Mild,Moderate,Severe,Emergency',
            'description' => 'required|string|max:1000',
            'contact_number' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        // Get user ID from session
        $userId = Session::get('user_id');
        if (!$userId) {
            Log::warning('Resident user ID not found in session for health status submission.');
            notify()->error('You must be logged in to submit a health report.');
            return redirect()->route('landing');
        }

        try {
            $healthStatus = new HealthStatus();
            $healthStatus->user_id = $userId;
            $healthStatus->concern_type = $request->concern_type;
            $healthStatus->severity = $request->severity;
            $healthStatus->description = $request->description;
            $healthStatus->contact_number = $request->contact_number;
            $healthStatus->emergency_contact = $request->emergency_contact;
            $healthStatus->status = 'pending'; // Default status for residents
            $healthStatus->save();

            notify()->success('Health report submitted successfully. Health officials will review your concern.');
            return redirect()->route('resident.health-status');
        } catch (\Exception $e) {
            Log::error("Error creating health status report: " . $e->getMessage());
            notify()->error('Error submitting health report: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show the announcements page (Recommendation).
     *
     * @return \Illuminate\View\View
     */
    public function announcements()
    {
        // For now, provide an empty collection since there's no Announcement model
        // In the future, you would fetch announcements from a database
        $announcements = collect();
        
        return view('resident.announcements', compact('announcements'));
    }
}