<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\HealthStatus;
use App\Models\Residents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HealthStatusRequestController
{
    /**
     * Display a listing of health status requests.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $healthRequests = HealthStatus::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalRequests = HealthStatus::count();
        $pendingRequests = HealthStatus::where('status', 'pending')->count();
        $reviewedRequests = HealthStatus::where('status', 'reviewed')->count();
        $inProgressRequests = HealthStatus::where('status', 'in_progress')->count();
        $resolvedRequests = HealthStatus::where('status', 'resolved')->count();

        return view('admin.health.health-status-requests', compact(
            'healthRequests',
            'totalRequests',
            'pendingRequests',
            'reviewedRequests',
            'inProgressRequests',
            'resolvedRequests'
        ));
    }

    /**
     * Display the specified health status request.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $healthRequest = HealthStatus::with('user')->findOrFail($id);
        
        return view('admin.health.show-health-status-request', compact('healthRequest'));
    }

    /**
     * Update the status of a health status request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,in_progress,resolved',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $healthRequest = HealthStatus::findOrFail($id);
        $healthRequest->status = $request->status;
        $healthRequest->admin_notes = $request->admin_notes;
        
        if ($request->status === 'reviewed' && $healthRequest->status !== 'reviewed') {
            $healthRequest->reviewed_at = now();
        }
        
        $healthRequest->save();

        notify()->success('Health status request updated successfully.');
        return redirect()->route('admin.health-status-requests.show', $id);
    }

    /**
     * Search health status requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = HealthStatus::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by concern type
        if ($request->filled('concern_type')) {
            $query->where('concern_type', 'like', '%' . $request->concern_type . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by resident name
        if ($request->filled('resident_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->resident_name . '%');
            });
        }

        $healthRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalRequests = HealthStatus::count();
        $pendingRequests = HealthStatus::where('status', 'pending')->count();
        $reviewedRequests = HealthStatus::where('status', 'reviewed')->count();
        $inProgressRequests = HealthStatus::where('status', 'in_progress')->count();
        $resolvedRequests = HealthStatus::where('status', 'resolved')->count();

        return view('admin.health.health-status-requests', compact(
            'healthRequests',
            'totalRequests',
            'pendingRequests',
            'reviewedRequests',
            'inProgressRequests',
            'resolvedRequests'
        ));
    }

    /**
     * Export health status requests to CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $query = HealthStatus::with('user');

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $healthRequests = $query->orderBy('created_at', 'desc')->get();

        $filename = 'health_status_requests_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($healthRequests) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Resident Name',
                'Concern Type',
                'Severity',
                'Description',
                'Contact Number',
                'Emergency Contact',
                'Status',
                'Admin Notes',
                'Created At',
                'Reviewed At'
            ]);

            // CSV data
            foreach ($healthRequests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->user->name ?? 'Unknown',
                    $request->concern_type,
                    $request->severity,
                    $request->description,
                    $request->contact_number,
                    $request->emergency_contact,
                    $request->status,
                    $request->admin_notes,
                    $request->created_at->format('Y-m-d H:i:s'),
                    $request->reviewed_at ? $request->reviewed_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
