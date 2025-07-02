<?php

namespace App\Helpers;

use App\Models\BarangayProfile;
use App\Models\Residents;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthHelper
{
    public static function attemptLogin($credentials, $remember)
    {
        // Check BarangayProfile first
        $user = BarangayProfile::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $remember);
            return $user;
        }

        // Check Resident if BarangayProfile not found
        $user = Residents::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $remember);
            return $user;
        }

        return false; // Authentication failed
    }
}
