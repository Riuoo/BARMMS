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
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:residents,email',
                'address' => 'required|string|max:500',
                'gender' => 'required|in:Male,Female',
                'contact_number' => 'required|string|max:255',
                'birth_date' => 'required|date|before:today',
                'marital_status' => 'required|in:Single,Married,Widowed,Divorced,Separated',
                'occupation' => 'required|string|max:255',
                'age' => 'required|integer|min:1|max:120',
                'family_size' => 'required|integer|min:1|max:20',
                'education_level' => 'required|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
                'income_level' => 'required|in:Low,Lower Middle,Middle,Upper Middle,High',
                'employment_status' => 'required|in:Unemployed,Part-time,Self-employed,Full-time',
                'is_pwd' => 'required|in:0,1',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_number' => 'nullable|string|max:255',
                'emergency_contact_relationship' => 'required|string|max:255',
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

            // Create a new Residents user with all demographic information
            $user = Residents::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'resident',
                'address' => $request->address,
                'gender' => $request->gender,
                'contact_number' => $request->contact_number,
                'birth_date' => $request->birth_date,
                'marital_status' => $request->marital_status,
                'occupation' => $request->occupation,
                'age' => $request->age,
                'family_size' => $request->family_size,
                'education_level' => $request->education_level,
                'income_level' => $request->income_level,
                'employment_status' => $request->employment_status,
                'is_pwd' => (bool) $request->is_pwd,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_number' => $request->emergency_contact_number,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'active' => true,
            ]);

            $accountRequest->status = 'completed';
            $accountRequest->token = null;
            $accountRequest->save();

            notify()->success('Registration successful! You can now log in.');
            return redirect()->route('landing');
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            notify()->error('There was a problem submitting your request. Please try again later or contact support.');
            return back()->withInput();
        }
    }
}