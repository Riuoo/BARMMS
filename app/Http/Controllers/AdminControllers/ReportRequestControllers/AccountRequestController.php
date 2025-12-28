<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\AccountRequest;
use App\Models\Residents;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use Illuminate\Http\Request;

class AccountRequestController
{
    public function accountRequest(Request $request)
    {
        $totalRequests = AccountRequest::count();
        $pendingCount = AccountRequest::where('status', 'pending')->count();
        $approvedCount = AccountRequest::where('status', 'approved')->count();
        $completedCount = AccountRequest::where('status', 'completed')->count();
        $rejectedCount = AccountRequest::where('status', 'rejected')->count();

        $query = AccountRequest::query();
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $accountRequests = $query->with('resident')->orderByRaw("FIELD(status, 'pending', 'approved', 'completed', 'rejected')")->orderByDesc('created_at')->paginate(10);
        
        // Check for duplicates and residency for each pending request
        foreach ($accountRequests as $accountRequest) {
            if ($accountRequest->status === 'pending') {
                // Check for duplicate by email in residents
                $duplicateByEmail = Residents::where('email', $accountRequest->email)->first();
                
                // Check for duplicate by name if available
                // Stricter validation: must match first name, last name, and handle middle name properly
                $duplicateByName = null;
                
                // Use separate fields if available, otherwise use full_name
                if ($accountRequest->first_name && $accountRequest->last_name) {
                    $requestFirstName = trim($accountRequest->first_name);
                    $requestMiddleName = trim($accountRequest->middle_name ?? '');
                    $requestLastName = trim($accountRequest->last_name);
                    $requestSuffix = trim($accountRequest->suffix ?? '');
                    
                    // Build full name from parts
                    $nameParts = array_filter([
                        $requestFirstName,
                        $requestMiddleName,
                        $requestLastName,
                        $requestSuffix
                    ], function($part) {
                        return !empty(trim($part ?? ''));
                    });
                    $fullName = implode(' ', $nameParts);
                    
                    // Try exact match first (case-insensitive, with all name parts)
                    $duplicateByName = Residents::whereRaw(
                        "LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ?",
                        [strtolower($requestFirstName), strtolower($requestLastName)]
                    )->where(function($query) use ($requestMiddleName, $requestSuffix) {
                        // Handle middle name: both must have it or both must not have it
                        if (!empty($requestMiddleName)) {
                            // Request has middle name - resident must also have one
                            $query->whereNotNull('middle_name')
                                  ->where('middle_name', '!=', '')
                                  ->whereRaw("LOWER(TRIM(middle_name)) = ?", [strtolower($requestMiddleName)]);
                        } else {
                            // Request has no middle name - resident must also not have one
                            $query->where(function($q) {
                                $q->whereNull('middle_name')
                                  ->orWhere('middle_name', '=', '');
                            });
                        }
                    })->where(function($query) use ($requestSuffix) {
                        // Handle suffix similarly
                        if (!empty($requestSuffix)) {
                            $query->whereRaw("LOWER(TRIM(COALESCE(suffix, ''))) = ?", [strtolower($requestSuffix)]);
                        } else {
                            $query->where(function($q) {
                                $q->whereNull('suffix')
                                  ->orWhere('suffix', '=', '');
                            });
                        }
                    })->first();
                    
                    // If no exact match with middle name handling, try first+last only match
                    // but flag it as a potential mismatch if middle names differ
                    if (!$duplicateByName) {
                        $firstLastMatch = Residents::whereRaw(
                            "LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ?",
                            [strtolower($requestFirstName), strtolower($requestLastName)]
                        )->where(function($query) use ($requestSuffix) {
                            if (!empty($requestSuffix)) {
                                $query->whereRaw("LOWER(TRIM(COALESCE(suffix, ''))) = ?", [strtolower($requestSuffix)]);
                            } else {
                                $query->where(function($q) {
                                    $q->whereNull('suffix')->orWhere('suffix', '=', '');
                                });
                            }
                        })->first();
                        
                        // If found but middle names differ, this is a mismatch that needs attention
                        if ($firstLastMatch) {
                            $residentMiddleName = trim($firstLastMatch->middle_name ?? '');
                            $hasMiddleNameMismatch = (!empty($requestMiddleName) && empty($residentMiddleName)) ||
                                                      (empty($requestMiddleName) && !empty($residentMiddleName));
                            
                            if ($hasMiddleNameMismatch) {
                                // Flag as duplicate but with middle name mismatch
                                $duplicateByName = $firstLastMatch;
                                $duplicateByName->middle_name_mismatch = true;
                            } else {
                                $duplicateByName = $firstLastMatch;
                            }
                        }
                    }
                } elseif ($accountRequest->full_name) {
                    // Fallback to full_name for old records using CONCAT
                    $duplicateByName = Residents::whereRaw(
                        "TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))) = ?",
                        [trim($accountRequest->full_name)]
                    )->first();
                    if (!$duplicateByName) {
                        $duplicateByName = Residents::whereRaw(
                            "LOWER(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))) LIKE ?",
                            ['%' . strtolower($accountRequest->full_name) . '%']
                        )->first();
                    }
                }
                
                // Check residency if address is available
                $residencyVerified = false;
                if ($accountRequest->address) {
                    $defaultBarangay = config('app.default_barangay', 'Lower Malinao');
                    $residencyVerified = str_contains($accountRequest->address, $defaultBarangay);
                }
                
                $accountRequest->duplicate_by_email = $duplicateByEmail;
                $accountRequest->duplicate_by_name = $duplicateByName;
                $accountRequest->residency_verified = $residencyVerified;
            }
        }
        
        return view('admin.requests.new-account-requests', compact('accountRequests', 'totalRequests', 'pendingCount', 'approvedCount', 'completedCount', 'rejectedCount'));
    }
    
    public function approveAccountRequest($id)
    {
        Log::info('approveAccountRequest method called with id: ' . $id);

        $accountRequest = AccountRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            // Check if pending duplicate request exists for the same email
            // This prevents approving a new request if an older one for the same email is still pending
            $existingRequest = AccountRequest::where('email', $accountRequest->email)
                ->where('status', 'pending')
                ->where('id', '!=', $accountRequest->id)
                ->first();

            if ($existingRequest) {
                AccountRequest::where('id', $existingRequest->id)->update(['status' => 'rejected']);
                DB::rollBack();
                Log::warning('Duplicate account request found for email: ' . $accountRequest->email . '. Older request rejected.');
                notify()->error('An account request with this email was already pending and has been rejected. Please try approving the correct one.');
                return redirect()->route('admin.new-account-requests');

            }

            // Generate token if it doesn't exist (should ideally be generated on request creation, but good fallback)
            if (!$accountRequest->token) {
                $accountRequest->token = Str::uuid();
            }

            // Get the ID of the currently logged-in admin user from the session
            $adminUserId = Session::get('user_id');
            Log::debug('Admin User ID from session: ' . $adminUserId);

            if ($adminUserId) {
                $accountRequest->barangay_profile_id = $adminUserId;
            } else {
                Log::warning('Admin User ID not found in session for account request approval: ' . $accountRequest->id);
            }

            // Update status to 'approved' and save the approver id
            $accountRequest->status = 'approved';
            $accountRequest->save();

            // If account request is linked to an existing resident, update the resident's email
            if ($accountRequest->resident_id) {
                $resident = Residents::find($accountRequest->resident_id);
                if ($resident) {
                    $resident->email = $accountRequest->email;
                    $resident->save();
                    Log::info('Updated resident email for resident_id: ' . $resident->id . ' with email: ' . $accountRequest->email);
                }
            }

            // Generate the full registration link for the email
            $registrationLink = route('register.form', ['token' => $accountRequest->token]);

            try {
                // Queue the email with the registration link (non-blocking)
                Mail::to($accountRequest->email)->queue(new AccountApproved($accountRequest->token));
                Log::info('Email queued successfully for: ' . $accountRequest->email);
            } catch (\Exception $e) {
                Log::error('Error queuing email for account request ' . $accountRequest->id . ': ' . $e->getMessage());
                // Don't rollback the transaction since email queuing failed, not the approval
                // The email can be retried later via queue retry mechanisms
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving account request ' . $accountRequest->id . ': ' . $e->getMessage());
            notify()->error('Error approving account request: ' . $e->getMessage());
            return redirect()->route('admin.new-account-requests');
            
        }

        $adminUserName = 'Admin';
        if ($adminUserId) {
            $adminUser = \App\Models\BarangayProfile::find($adminUserId);
            if (!$adminUser) {
                $adminUser = \App\Models\Residents::find($adminUserId);
            }
            if ($adminUser && property_exists($adminUser, 'name')) {
                $adminUserName = $adminUser->full_name;
            }
        }

        notify()->success("Account request approved by {$adminUserName} and email queued for delivery.");
        return redirect()->route('admin.requests.new-account-requests');
            
    }

    public function rejectAccountRequest(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        Log::info('rejectAccountRequest method called with id: ' . $id);

        $accountRequest = AccountRequest::findOrFail($id);

        if ($accountRequest->status !== 'pending') {
            notify()->error('Only pending account requests can be rejected.');
            return redirect()->route('admin.requests.new-account-requests');
        }

        try {
            DB::beginTransaction();

            // Check if this is a duplicate rejection
            $isDuplicate = false;
            $duplicateByEmail = Residents::where('email', $accountRequest->email)->first();
            $duplicateByName = null;
            
            if ($accountRequest->full_name) {
                $duplicateByName = Residents::whereRaw(
                    "LOWER(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))) LIKE ?",
                    ['%' . strtolower($accountRequest->full_name) . '%']
                )->first();
            }
            
            if ($duplicateByEmail || $duplicateByName) {
                $isDuplicate = true;
            }

            // Get the ID of the currently logged-in admin user from the session
            $adminUserId = Session::get('user_id');
            Log::debug('Admin User ID from session: ' . $adminUserId);

            if ($adminUserId) {
                $accountRequest->barangay_profile_id = $adminUserId;
            } else {
                Log::warning('Admin User ID not found in session for account request rejection: ' . $accountRequest->id);
            }

            // Update status to 'rejected' and save rejection reason
            $accountRequest->status = 'rejected';
            $accountRequest->rejection_reason = $request->rejection_reason;
            $accountRequest->save();

            try {
                // Queue the rejection email
                Mail::to($accountRequest->email)->queue(new AccountRejected($request->rejection_reason, $isDuplicate));
                Log::info('Rejection email queued successfully for: ' . $accountRequest->email);
            } catch (\Exception $e) {
                Log::error('Error queuing rejection email for account request ' . $accountRequest->id . ': ' . $e->getMessage());
                // Don't rollback the transaction since email queuing failed, not the rejection
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting account request ' . $accountRequest->id . ': ' . $e->getMessage());
            notify()->error('Error rejecting account request: ' . $e->getMessage());
            return redirect()->route('admin.requests.new-account-requests');
        }

        $adminUserName = 'Admin';
        if ($adminUserId) {
            $adminUser = \App\Models\BarangayProfile::find($adminUserId);
            if (!$adminUser) {
                $adminUser = \App\Models\Residents::find($adminUserId);
            }
            if ($adminUser && property_exists($adminUser, 'name')) {
                $adminUserName = $adminUser->full_name;
            }
        }

        notify()->success("Account request rejected by {$adminUserName} and email notification sent.");
        return redirect()->route('admin.requests.new-account-requests');
    }
}
