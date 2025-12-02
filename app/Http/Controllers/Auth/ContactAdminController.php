<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\AccountRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Residents;
class ContactAdminController
{
    public function contactAdmin()
    {
        return view('login.admin_contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the email already exists in the residents table
        $residentExists = Residents::where('email', $request->email)->exists();

        if ($residentExists) {
            notify()->error('This email is already registered. Please log in or use a different email address.');
            return back();
        }

        // Check if the email already exists in the account_requests table
        $existingRequest = AccountRequest::where('email', $request->email)->first();

        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                notify()->error('An account request for this email is already pending. Please wait for administrator approval or check your email for updates.');
                return back();
            } elseif ($existingRequest->status === 'approved') {
                notify()->error('An account request for this email has already been approved. Please check your email for the registration link.');
                return back();
            } elseif ($existingRequest->status === 'completed') {
                notify()->error('An account with this email already exists. Please log in or use a different email address.');
                return back();
            }
        }

        // Create new account request
        try {
            AccountRequest::create([
                'email' => $request->email,
                'status' => 'pending',
                'token' => Str::uuid(),
            ]);
            notify()->success('Your account request was submitted! Please check your email for updates from the administrator.');
            return back();
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violation specifically
            if ($e->getCode() == 23000) { // SQLSTATE[23000]: Integrity constraint violation
                Log::error('Duplicate email attempt: ' . $request->email);
                notify()->error('An account request for this email already exists. Please check your email or contact support.');
            } else {
                Log::error('Error storing account request: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                notify()->error('There was a problem submitting your request. Please try again later or contact support.');
            }
            return back()->withInput();
        } catch (\Exception $e) {
            Log::error('Error storing account request: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            notify()->error('There was a problem submitting your request. Please try again later or contact support.');
            return back()->withInput();
        }
    }
}