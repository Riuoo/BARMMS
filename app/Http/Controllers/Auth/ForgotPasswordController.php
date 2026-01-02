<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Residents;
use App\Models\BarangayProfile;
use App\Services\BrevoEmailService;

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
        // Note: PasswordResetMail computes resetUrl and expires internally
        $emailService = app(BrevoEmailService::class);
        $emailService->queueEmail(
            $request->email,
            'Password Reset Request',
            'emails.password-reset',
            [
                'token' => $token,
                'email' => $request->email
            ]
        );

        notify()->success('Password reset link sent!');
        return back();
    }
}
