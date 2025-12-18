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
            'first_name' => 'required|string|max:255',
            'middle_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    // Skip validation if "no middle name" checkbox is checked (middle_name will be empty/disabled)
                    if ($request->has('no_middle_name') || empty(trim($value ?? ''))) {
                        return;
                    }
                        $trimmed = trim($value);
                        // Check if it's a single letter
                        if (strlen($trimmed) === 1) {
                            $fail('Please enter your full middle name. Initials are not allowed.');
                        }
                        // Check if it's an initial with a period (e.g., "A." or "A. ")
                        if (preg_match('/^[A-Za-z]\.\s*$/', $trimmed)) {
                            $fail('Please enter your full middle name. Initials are not allowed.');
                        }
                        // Check if it's less than 2 characters after removing periods and spaces
                        $cleaned = preg_replace('/[.\s]+/', '', $trimmed);
                        if (strlen($cleaned) < 2) {
                            $fail('Please enter your full middle name. Initials are not allowed.');
                    }
                },
            ],
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|in:Jr.,Sr.,II,III,IV',
            'email' => 'required|email|max:255',
            'verification_documents' => 'required|array|min:1',
            'verification_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max per file
        ]);

        // Construct full name (include suffix if provided)
        $nameParts = [
            $request->first_name,
            $request->middle_name ?? '',
            $request->last_name,
            $request->suffix ?? ''
        ];
        $fullName = trim(implode(' ', array_filter($nameParts, function($part) {
            return !empty(trim($part));
        })));
        $fullName = preg_replace('/\s+/', ' ', $fullName); // Remove extra spaces

        // Check if the name matches an existing resident using fuzzy matching
        $matchedResident = null;
        
        // Try exact match first (case-insensitive)
        $matchedResident = Residents::whereRaw(
            "LOWER(TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')))) = ?",
            [strtolower($fullName)]
        )->first();
        
        // If no exact match, try partial/fuzzy match
        if (!$matchedResident) {
            $matchedResident = Residents::whereRaw(
                "LOWER(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))) LIKE ?",
                ['%' . strtolower($fullName) . '%']
            )->first();
        }
        
        // Also check without middle name (first + last only) for fuzzy matching
        if (!$matchedResident) {
            $firstLast = trim($request->first_name . ' ' . $request->last_name);
            $matchedResident = Residents::whereRaw(
                "LOWER(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) LIKE ?",
                ['%' . strtolower($firstLast) . '%']
            )->first();
        }

        // Enforce that the name must exist in resident records
        if (!$matchedResident) {
            notify()->error('We could not find your name in the barangay resident records. Please visit the barangay office to verify or update your information before requesting an account.');
            return back()->withInput();
        }

        // If name matches an existing resident, ensure they don't already have an account
        if (!empty($matchedResident->email)) {
            notify()->error('This name already has an existing account. Please verify at the barangay office.');
            return back()->withInput();
        }

        // Check if the email already exists in the residents table
        $residentExists = Residents::where('email', $request->email)->exists();

        if ($residentExists) {
            notify()->error('This email is already registered. Please log in or use a different email address.');
            return back()->withInput();
        }

        // Check if the email already exists in the account_requests table
        $existingRequest = AccountRequest::where('email', $request->email)->first();

        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                notify()->error('An account request for this email is already pending. Please wait for administrator approval or check your email for updates.');
                return back()->withInput();
            } elseif ($existingRequest->status === 'approved') {
                notify()->error('An account request for this email has already been approved. Please check your email for the registration link.');
                return back()->withInput();
            } elseif ($existingRequest->status === 'completed') {
                notify()->error('An account with this email already exists. Please log in or use a different email address.');
                return back()->withInput();
            }
        }

        // Handle file uploads
        $verificationDocuments = [];
        if ($request->hasFile('verification_documents')) {
            foreach ($request->file('verification_documents') as $file) {
                $path = $file->store('account_verification_documents', 'public');
                $verificationDocuments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        // Create new account request
        try {
            AccountRequest::create([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'middle_name' => !empty(trim($request->middle_name ?? '')) ? trim($request->middle_name) : null,
                'last_name' => $request->last_name,
                'suffix' => !empty(trim($request->suffix ?? '')) ? trim($request->suffix) : null,
                'full_name' => $fullName,
                'status' => 'pending',
                'token' => Str::uuid(),
                'verification_documents' => $verificationDocuments,
                'resident_id' => $matchedResident ? $matchedResident->id : null,
            ]);
            notify()->success('Your account request was submitted successfully! Please check your email for updates from the administrator.');
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