<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\BarangayProfile;
use App\Models\Residents;
use App\Http\Requests\LoginRequest;
use App\Services\TwoFactorAuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

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

            // Check if 2FA is enabled
            if ($user->hasTwoFactorEnabled()) {
                // Check if device is trusted
                $deviceId = $this->twoFactorService->generateDeviceId($request);
                
                if ($this->twoFactorService->isDeviceTrusted($user->id, $deviceId)) {
                    // Device is trusted, complete login
                    $request->session()->regenerate();
                    
                    // Get device fingerprint for cookie (use stored if available)
                    $deviceFingerprint = Session::get('device_fingerprint') 
                        ?? $this->twoFactorService->getDeviceFingerprint($request);
                    
                    session([
                        'user_id' => $user->id,
                        'user_role' => $user->role,
                        '2fa_device_id' => $deviceId,
                        'device_fingerprint' => $deviceFingerprint,
                    ]);
                    
                    // Prepare response with cookie
                    $response = null;
                    if ($user->role === 'nurse') {
                        $response = redirect()->intended(route('admin.health-reports'));
                    } else {
                        $response = redirect()->intended(route('admin.dashboard'));
                    }
                    
                    // Ensure device fingerprint cookie is set
                    if (!$request->cookie('device_fingerprint')) {
                        $response->cookie(
                            'device_fingerprint',
                            $deviceFingerprint,
                            30 * 24 * 60, // 30 days in minutes
                            '/', // path
                            null, // domain (null = current domain)
                            false, // secure (false for local HTTP)
                            true, // httpOnly
                            false, // raw
                            'lax' // sameSite
                        );
                    }
                    
                    return $response;
                } else {
                    // Device not trusted, require 2FA verification
                    $request->session()->regenerate();
                    
                    // Generate and store device ID BEFORE redirecting to verification
                    // This ensures consistent device ID throughout the verification process
                    $deviceId = $this->twoFactorService->generateDeviceId($request);
                    $deviceFingerprint = $this->twoFactorService->getDeviceFingerprint($request);
                    
                    session([
                        '2fa_pending_user_id' => $user->id,
                        '2fa_pending_user_role' => $user->role,
                        '2fa_pending_device_id' => $deviceId,
                        '2fa_pending_device_fingerprint' => $deviceFingerprint,
                    ]);
                    
                    return redirect()->route('2fa.verify');
                }
            } else {
                // 2FA not enabled, proceed with normal login
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
        }

        // Check Resident if BarangayProfile not found
        $user = Residents::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->active) {
                notify()->error('Your account is disabled. Please contact admin.');
                return back()->onlyInput('email');
            }

            // Check if 2FA is enabled
            if ($user->hasTwoFactorEnabled()) {
                // Check if device is trusted
                $deviceId = $this->twoFactorService->generateDeviceId($request);
                
                if ($this->twoFactorService->isDeviceTrusted($user->id, $deviceId)) {
                    // Device is trusted, complete login
                    $request->session()->regenerate();
                    
                    // Get device fingerprint for cookie (use stored if available)
                    $deviceFingerprint = Session::get('device_fingerprint') 
                        ?? $this->twoFactorService->getDeviceFingerprint($request);
                    
                    session([
                        'user_id' => $user->id,
                        'user_role' => 'resident',
                        '2fa_device_id' => $deviceId,
                        'device_fingerprint' => $deviceFingerprint,
                    ]);
                    
                    // Prepare response with cookie
                    $response = redirect()->intended(route('resident.dashboard'));
                    
                    // Ensure device fingerprint cookie is set
                    if (!$request->cookie('device_fingerprint')) {
                        $response->cookie(
                            'device_fingerprint',
                            $deviceFingerprint,
                            30 * 24 * 60, // 30 days in minutes
                            '/', // path
                            null, // domain (null = current domain)
                            false, // secure (false for local HTTP)
                            true, // httpOnly
                            false, // raw
                            'lax' // sameSite
                        );
                    }
                    
                    return $response;
                } else {
                    // Device not trusted, require 2FA verification
                    $request->session()->regenerate();
                    
                    // Generate and store device ID BEFORE redirecting to verification
                    $deviceId = $this->twoFactorService->generateDeviceId($request);
                    $deviceFingerprint = $this->twoFactorService->getDeviceFingerprint($request);
                    
                    session([
                        '2fa_pending_user_id' => $user->id,
                        '2fa_pending_user_role' => 'resident',
                        '2fa_pending_device_id' => $deviceId,
                        '2fa_pending_device_fingerprint' => $deviceFingerprint,
                    ]);
                    
                    return redirect()->route('2fa.verify');
                }
            } else {
                // 2FA not enabled, proceed with normal login
                $request->session()->regenerate();
                session([
                    'user_id' => $user->id,
                    'user_role' => 'resident'
                ]);
                
                return redirect()->intended(route('resident.dashboard'));
            }
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
