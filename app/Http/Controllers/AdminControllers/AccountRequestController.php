<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\AccountRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Mail\AccountApproved;

class AccountRequestController
{
    /**
     * Display a listing of the account requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountRequest()
    {
        $accountRequests = AccountRequest::orderBy('created_at', 'desc')->get();
        return view('admin.new-account-requests', compact('accountRequests'));
    }

    /**
     * Approve the specified account request by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
                ->where('id', '!=', $accountRequest->id) // Exclude the current request being processed
                ->first();

            if ($existingRequest) {
                // If a duplicate pending request exists, reject it and rollback the current transaction
                AccountRequest::where('id', $existingRequest->id)->update(['status' => 'rejected']);
                DB::rollBack();
                Log::warning('Duplicate account request found for email: ' . $accountRequest->email . '. Older request rejected.');
                return redirect()->route('admin.new-account-requests')
                    ->with('error', 'An account request with this email was already pending and has been rejected. Please try approving the correct one.');
            }

            // Generate token if it doesn't exist (should ideally be generated on request creation, but good fallback)
            if (!$accountRequest->token) {
                $accountRequest->token = Str::uuid();
            }

            // Get the ID of the currently logged-in admin user from the session
            $adminUserId = Session::get('user_id');
            Log::debug('Admin User ID from session: ' . $adminUserId); // For debugging purposes

            if ($adminUserId) {
                $accountRequest->user_id = $adminUserId; // Assign the admin's user_id
            } else {
                // Log a warning if the admin user ID is not found in the session
                Log::warning('Admin User ID not found in session for account request approval: ' . $accountRequest->id);
                // Optionally, you could assign a default 'system' user_id or prevent approval here
                // For now, it will just leave user_id as null if not found, which might be desired if it's nullable
            }

            // Update status to 'approved' and save the user_id
            $accountRequest->status = 'approved'; // Explicitly set status
            $accountRequest->save(); // Save changes including user_id and status

            // Generate the full registration link for the email
            $registrationLink = route('register.form', ['token' => $accountRequest->token]);

            try {
                // Send the email with the registration link
                Mail::to($accountRequest->email)->send(new AccountApproved($accountRequest->token));
                Log::info('Email sent successfully to: ' . $accountRequest->email);
            } catch (\Exception $e) {
                Log::error('Error sending email for account request ' . $accountRequest->id . ': ' . $e->getMessage());
                // If email sending fails, you might want to rollback the approval or just warn
                // For now, we'll proceed with the approval but show an error about the email
                DB::rollBack(); // Rollback the transaction if email fails
                return redirect()->route('admin.new-account-requests')->with('error', 'Account request approved, but email sending failed. Error: ' . $e->getMessage());
            }

            DB::commit(); // Commit the transaction if everything above succeeded

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurred within the transaction
            Log::error('Error approving account request ' . $accountRequest->id . ': ' . $e->getMessage());
            return redirect()->route('admin.new-account-requests')->with('error', 'Error approving account request: ' . $e->getMessage());
        }

        // Prepare success message with admin name
        $adminUserName = 'Admin';
        if ($adminUserId) {
            // Attempt to find the admin's name from either BarangayProfile or Residence model
            // This assumes admin users are stored in one of these tables
            $adminUser = \App\Models\BarangayProfile::find($adminUserId);
            if (!$adminUser) {
                $adminUser = \App\Models\Residence::find($adminUserId);
            }
            if ($adminUser && property_exists($adminUser, 'name')) {
                $adminUserName = $adminUser->name;
            }
        }

        return redirect()->route('admin.new-account-requests')->with('success', "Account request approved by {$adminUserName} and email sent.");
    }
}
