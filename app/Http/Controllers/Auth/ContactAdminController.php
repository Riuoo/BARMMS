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
        // Only block if there's a pending request - allow if previous requests were rejected/completed
        $existingPendingRequest = AccountRequest::where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existingPendingRequest) {
            notify()->error('An account request for this email is already pending. Please wait for administrator approval or check your email for updates.');
            return back();
        }

        // Allow new requests even if there are old rejected/completed ones
        try {
            AccountRequest::create([
                'email' => $request->email,
                'status' => 'pending',
                'token' => Str::uuid(),
            ]);
            notify()->success('Your account request was submitted! Please check your email for updates from the administrator.');
            return back();
        } catch (\Exception $e) {
            Log::error('Error storing account request: ' . $e->getMessage());
            notify()->error('There was a problem submitting your request. Please try again later or contact support.');
            return back();
        }
    }
}