<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApproved;

class AccountRequestController extends Controller
{
    /**
     * Approve the specified account request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountRequest  $accountRequest
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, AccountRequest $accountRequest)
    {
        $accountRequest->status = 'approved';
        $accountRequest->user_id = $request->session()->get('user_id'); // Set admin user_id who approved
        $accountRequest->save();

        Mail::to($accountRequest->email)->send(new AccountApproved($accountRequest->token));

        $adminUserId = $request->session()->get('user_id');
        $adminUserName = 'Admin';
        if ($adminUserId) {
            $adminUser = \App\Models\BarangayProfile::find($adminUserId);
            if (!$adminUser) {
                $adminUser = \App\Models\Residence::find($adminUserId);
            }
            if ($adminUser) {
                $adminUserName = $adminUser->name;
            }
        }

        return redirect()->route('admin.new-account-requests')->with('success', "Account request approved by {$adminUserName} and email sent.");
    }
}
