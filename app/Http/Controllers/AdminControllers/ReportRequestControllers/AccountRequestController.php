<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\AccountRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Mail\AccountApproved;
use Illuminate\Http\Request;

class AccountRequestController
{
    public function accountRequest(Request $request)
    {
        $totalRequests = AccountRequest::count();
        $pendingCount = AccountRequest::where('status', 'pending')->count();
        $approvedCount = AccountRequest::where('status', 'approved')->count();
        $completedCount = AccountRequest::where('status', 'completed')->count();

        $query = AccountRequest::query();
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where('email', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $accountRequests = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'completed')")->orderByDesc('created_at')->paginate(10);
        return view('admin.requests.new-account-requests', compact('accountRequests', 'totalRequests', 'pendingCount', 'approvedCount', 'completedCount'));
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
                $adminUserName = $adminUser->name;
            }
        }

        notify()->success("Account request approved by {$adminUserName} and email queued for delivery.");
        return redirect()->route('admin.requests.new-account-requests');
            
    }
}
