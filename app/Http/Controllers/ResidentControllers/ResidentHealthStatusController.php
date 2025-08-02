<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\HealthStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ResidentHealthStatusController
{
    /**
     * Show the health status reporting page.
     *
     * @return \Illuminate\View\View
     */
    public function healthStatus()
    {
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
} 