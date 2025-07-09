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
        return view('admin_contact');
    }

    /**
     * Store a new account request from the contact form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the email already exists in the residents table
        $residentExists = Residents::where('email', $request->email)->exists();

        if ($residentExists) {
            return response()->json(['error' => 'An account with this email already exists. Please log in instead.'], 400);
        }

        // Check if the email already exists in the account_requests table
        $existingRequest = AccountRequest::where('email', $request->email)->first();

        if ($existingRequest) {
            return response()->json(['error' => 'An account request with this email is already pending. Please wait for administrator approval.'], 400);
        }

        try {
            AccountRequest::create([
                'email' => $request->email,
                'status' => 'pending',
                'token' => Str::uuid(),
            ]);
            return response()->json(['success' => 'Your account request has been submitted successfully. Please wait for administrator approval.']);
        } catch (\Exception $e) {
            Log::error('Error storing account request: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to submit account request. Please try again later.'], 500);
        }
    }
}