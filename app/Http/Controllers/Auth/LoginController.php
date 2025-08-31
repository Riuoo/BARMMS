<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\BarangayProfile;
use App\Models\Residents;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;

class LoginController
{
    public function showLoginForm()
    {
        return view('login.landing');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Check BarangayProfile first
        $user = BarangayProfile::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->active) {
                notify()->error('Your account is disabled. Please contact admin.');
                return back()->onlyInput('email');
            }
            // Don't use Auth::login() for barangay profiles, just use session
            $request->session()->regenerate();
            session([
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            
                // Redirect based on role
            if ($user->role === 'nurse') {
                return redirect()->intended(route('admin.health-reports'));
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        // Check Resident if BarangayProfile not found
        $user = Residents::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->active) {
                notify()->error('Your account is disabled. Please contact admin.');
                return back()->onlyInput('email');
            }
            // Don't use Auth::login() for residents, just use session
            $request->session()->regenerate();
            session(['user_id' => $user->id]);
            session(['user_role' => 'resident']);
            
            return redirect()->intended(route('resident.dashboard'));
        }

        notify()->error('The provided credentials do not match our records.');
        return back()->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
