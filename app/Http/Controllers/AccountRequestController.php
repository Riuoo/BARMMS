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
        $accountRequest->save();

        Mail::to($accountRequest->email)->send(new AccountApproved());

        return redirect()->route('admin.new-account-requests')->with('success', 'Account request approved and email sent.');
    }
}
