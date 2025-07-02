<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\AccountRequest;
use App\Models\Residents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegistrationController
{
    public function showRegistrationForm($token)
    {
        $accountRequest = AccountRequest::where('token', $token)->where('status', 'approved')->first();

        if (!$accountRequest) {
            return redirect()->route('landing')->with('error', 'Invalid registration link.');
        }

        return view('signup.signup', compact('token', 'accountRequest'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:residents,email', // Ensure email is unique in residents table
            'address' => 'required|string|max:500', // Added address validation
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
            // 'role' is now hardcoded in the form, so no need to validate it here from user input
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $accountRequest = AccountRequest::where('token', $request->token)->where('status', 'approved')->first();

        if (!$accountRequest) {
            return redirect()->route('landing')->with('error', 'Invalid registration link.');
        }

        // Create a new Residents user
        $user = Residents::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'resident', // Explicitly set role to 'resident'
            'address' => $request->address, // Save the address
        ]);

        // Update the account request status
        $accountRequest->status = 'completed';
        $accountRequest->token = null; // Invalidate the token after use
        $accountRequest->save();

        // Optionally, log the user in (uncomment if desired)
        // auth()->login($user);

        return redirect()->route('landing')->with('success', 'Registration successful! You can now log in.');
    }
}