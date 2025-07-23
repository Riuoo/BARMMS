<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Residents;
use App\Models\BarangayProfile;

class ResetPasswordController
{
    /**
     * Display the password reset view.
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Validate token
        $resetRecord = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addHours(1)->isPast()) {
            notify()->error('Invalid or expired token.');
            return redirect()->route('password.request');
        }

        return view('reset-password', [
            'token' => $token,
            'email' => $resetRecord->email
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Validate token and email
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            notify()->error('Invalid token or email.');
            return back();
        }

        // Update password in the correct table
        $user = Residents::where('email', $request->email)->first();
        $userType = 'resident';

        if (!$user) {
            $user = BarangayProfile::where('email', $request->email)->first();
            $userType = 'barangay_profile';
        }

        if (!$user) {
            notify()->error('User not found.');
            return back();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clear the token
        DB::table('password_resets')->where('email', $request->email)->delete();

        notify()->success('Password updated!');
        return redirect('/login');
    }
}
