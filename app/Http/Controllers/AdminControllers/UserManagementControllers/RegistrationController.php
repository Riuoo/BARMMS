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
        $accountRequest = AccountRequest::with('resident')->where('token', $token)->where('status', 'approved')->first();

        if (!$accountRequest) {
            notify()->error('Invalid registration link.');
            return redirect()->route('landing');
            
        }

        $resident = null;
        if ($accountRequest->resident_id && $accountRequest->resident) {
            $resident = $accountRequest->resident;
        }

        return view('login.signup', compact('token', 'accountRequest', 'resident'));
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => [
                    'nullable',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if ($value && !empty(trim($value))) {
                            $trimmed = trim($value);
                            // Check if it's a single letter
                            if (strlen($trimmed) === 1) {
                                $fail('Please enter your full middle name. Initials are not allowed.');
                            }
                            // Check if it's an initial with a period (e.g., "A." or "A. ")
                            if (preg_match('/^[A-Za-z]\.\s*$/', $trimmed)) {
                                $fail('Please enter your full middle name. Initials are not allowed.');
                            }
                            // Check if it's just an initial without period but only one character (already handled above)
                            // Additional check: if it's less than 2 characters after removing periods and spaces
                            $cleaned = preg_replace('/[.\s]+/', '', $trimmed);
                            if (strlen($cleaned) < 2) {
                                $fail('Please enter your full middle name. Initials are not allowed.');
                            }
                        }
                    },
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        $accountRequest = AccountRequest::where('token', $request->token)->where('status', 'approved')->first();
                        if ($accountRequest && $accountRequest->resident_id) {
                            // If linked to existing resident, allow same email
                            $resident = Residents::find($accountRequest->resident_id);
                            if ($resident && $resident->email === $value) {
                                return; // Same email is allowed for existing resident
                            }
                        }
                        // Check uniqueness for new residents
                        if (Residents::where('email', $value)->exists()) {
                            $fail('The email has already been taken.');
                        }
                    },
                ],
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
                'emergency_contact_name' => 'required|string|max:255',
                'emergency_contact_number' => 'required|string|max:255',
                'emergency_contact_relationship' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
                'token' => 'required|string',
                'privacy_consent' => 'required|accepted',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $accountRequest = AccountRequest::with('resident')->where('token', $request->token)->where('status', 'approved')->first();

            if (!$accountRequest) {
                notify()->error('Invalid registration link.');
                return redirect()->route('landing');
            }

            // Check if account request is linked to an existing resident
            if ($accountRequest->resident_id && $accountRequest->resident) {
                // Update existing resident record with all information from the form
                $resident = $accountRequest->resident;
                $resident->email = $request->email;
                $resident->contact_number = $request->contact_number;
                $resident->password = Hash::make($request->password);
                $resident->birth_date = $request->birth_date;
                $resident->marital_status = $request->marital_status;
                $resident->occupation = $request->occupation;
                $resident->age = $request->age;
                $resident->family_size = $request->family_size;
                $resident->education_level = $request->education_level;
                $resident->income_level = $request->income_level;
                $resident->employment_status = $request->employment_status;
                $resident->is_pwd = (bool) $request->is_pwd;
                $resident->emergency_contact_name = $request->emergency_contact_name;
                $resident->emergency_contact_number = $request->emergency_contact_number;
                $resident->emergency_contact_relationship = $request->emergency_contact_relationship;
                $resident->active = true;
                $resident->save();
                $user = $resident;
            } else {
                // Create a new Residents user with all demographic information
                $user = Residents::create([
                    'first_name' => $request->first_name,
                    'middle_name' => !empty(trim($request->middle_name ?? '')) ? trim($request->middle_name) : null,
                    'last_name' => $request->last_name,
                    'suffix' => !empty(trim($request->suffix ?? '')) ? trim($request->suffix) : null,
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
            }

            $accountRequest->status = 'completed';
            $accountRequest->token = null;
            // Update full_name for backward compatibility
            $nameParts = array_filter([
                $request->first_name,
                $request->middle_name ?? '',
                $request->last_name,
                $request->suffix ?? ''
            ], function($part) {
                return !empty(trim($part ?? ''));
            });
            $accountRequest->full_name = implode(' ', $nameParts);
            $accountRequest->address = $request->address;
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