<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Residents;
use App\Models\BarangayProfile;

class ForgotPasswordController
{
    public function showLinkRequestForm()
    {
        return view('login.fpass');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Check email in both tables
        $user = Residents::where('email', $request->email)->first() 
                ?? BarangayProfile::where('email', $request->email)->first();

        if (!$user) {
            notify()->error('Email not found in our records.');
            return back();
        }

        // Generate and store token
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'resident_id' => $user instanceof Residents ? $user->id : null,
            'barangay_profile_id' => $user instanceof BarangayProfile ? $user->id : null,
            'created_at' => Carbon::now()
        ]);

        // Send email via queue (non-blocking)
        Mail::to($request->email)->queue(
            new \App\Mail\PasswordResetMail($token, $request->email)
        );

        notify()->success('Password reset link sent!');
        return back();
    }
}
