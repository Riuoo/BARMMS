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

        return view('login.reset-password', [
            'token' => $token,
            'email' => $resetRecord->email
        ]);
    }

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

        // Update password in the correct table using foreign keys when available
        $user = null;
        $userType = null;

        if (isset($resetRecord->resident_id) && $resetRecord->resident_id) {
            $user = Residents::find($resetRecord->resident_id);
            $userType = 'resident';
        } elseif (isset($resetRecord->barangay_profile_id) && $resetRecord->barangay_profile_id) {
            $user = BarangayProfile::find($resetRecord->barangay_profile_id);
            $userType = 'barangay_profile';
        } else {
            // Fallback for legacy records without FKs
            $user = Residents::where('email', $request->email)->first();
            $userType = $user ? 'resident' : null;
            if (!$user) {
                $user = BarangayProfile::where('email', $request->email)->first();
                $userType = $user ? 'barangay_profile' : null;
            }
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
