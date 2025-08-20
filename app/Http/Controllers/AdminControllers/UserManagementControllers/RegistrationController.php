<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

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
            notify()->error('Invalid registration link.');
            return redirect()->route('landing');
            
        }

        return view('login.signup', compact('token', 'accountRequest'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:residents,email',
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $accountRequest = AccountRequest::where('token', $request->token)->where('status', 'approved')->first();

        if (!$accountRequest) {
            notify()->error('Invalid registration link.');
            return redirect()->route('landing');
            
        }

        // Create a new Residents user
        $user = Residents::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'resident',
            'address' => $request->address,
        ]);

        $accountRequest->status = 'completed';
        $accountRequest->token = null;
        $accountRequest->save();

        notify()->success('Registration successful! You can now log in.');
        return redirect()->route('landing');
            
    }
}