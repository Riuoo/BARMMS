<?php

namespace App\Http\Controllers\LoginControllers;

use Illuminate\Http\Request;
use App\Models\AccountRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            'email' => 'required|email|unique:account_requests,email',
        ]);

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
