<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\AccountRequest;
use App\Models\User;
use App\Models\BarangayProfile;
use App\Models\Residence;
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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $accountRequest = AccountRequest::where('token', $request->token)->where('status', 'approved')->first();

        if (!$accountRequest) {
            return redirect()->route('landing')->with('error', 'Invalid registration link.');
        }

        if ($request->role === 'barangay') {
            $user = BarangayProfile::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'barangay',
            ]);
        } elseif ($request->role === 'residence') {
            $user = Residence::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'residence',
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user', // Default role
            ]);
        }

        $accountRequest->status = 'completed';
        $accountRequest->token = null;
        $accountRequest->save();

        // Optionally, log the user in
        // auth()->login($user);

        return redirect()->route('landing')->with('success', 'Registration successful! You can now log in.');
    }
}
