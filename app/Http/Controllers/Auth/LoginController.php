<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\BarangayProfile;
use App\Models\Residents;
use App\Http\Requests\LoginRequest;
use App\Services\TwoFactorAuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function showLoginForm(Request $request)
    {
        // Check for remember token cookie and auto-login if valid
        if ($request->hasCookie('remember_token')) {
            $rememberToken = $request->cookie('remember_token');
            
            // Check BarangayProfile first
            $users = BarangayProfile::whereNotNull('remember_token')->get();
            foreach ($users as $user) {
                if (Hash::check($rememberToken, $user->remember_token)) {
                    if ($user->active) {
                        // Check if 2FA is enabled
                        if ($user->hasTwoFactorEnabled()) {
                            $deviceId = $this->twoFactorService->generateDeviceId($request);
                            if ($this->twoFactorService->isDeviceTrusted($user->id, $deviceId)) {
                                $request->session()->regenerate();
                                $deviceFingerprint = Session::get('device_fingerprint') 
                                    ?? $this->twoFactorService->getDeviceFingerprint($request);
                                
                                session([
                                    'user_id' => $user->id,
                                    'user_role' => $user->role,
                                    '2fa_device_id' => $deviceId,
                                    'device_fingerprint' => $deviceFingerprint,
                                ]);
                                
                                if ($user->role === 'nurse') {
                                    return redirect()->intended(route('admin.health-reports'));
                                }
                                return redirect()->intended(route('admin.dashboard'));
                            }
                        } else {
                            // No 2FA, auto-login
                            $request->session()->regenerate();
                            session([
                                'user_id' => $user->id,
                                'user_role' => $user->role
                            ]);
                            
                            if ($user->role === 'nurse') {
                                return redirect()->intended(route('admin.health-reports'));
                            }
                            return redirect()->intended(route('admin.dashboard'));
                        }
                    }
                }
            }
            
            // Check Residents
            $residents = Residents::whereNotNull('remember_token')->get();
            foreach ($residents as $user) {
                if (Hash::check($rememberToken, $user->remember_token)) {
                    if ($user->active) {
                        // Check if 2FA is enabled
                        if ($user->hasTwoFactorEnabled()) {
                            $deviceId = $this->twoFactorService->generateDeviceId($request);
                            if ($this->twoFactorService->isDeviceTrusted($user->id, $deviceId)) {
                                $request->session()->regenerate();
                                $deviceFingerprint = Session::get('device_fingerprint') 
                                    ?? $this->twoFactorService->getDeviceFingerprint($request);
                                
                                session([
                                    'user_id' => $user->id,
                                    'user_role' => 'resident',
                                    '2fa_device_id' => $deviceId,
                                    'device_fingerprint' => $deviceFingerprint,
                                ]);
                                
                                return redirect()->intended(route('resident.dashboard'));
                            }
                        } else {
                            // No 2FA, auto-login
                            $request->session()->regenerate();
                            session([
                                'user_id' => $user->id,
                                'user_role' => 'resident'
                            ]);
                            
                            return redirect()->intended(route('resident.dashboard'));
                        }
                    }
                }
            }
        }
        
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
                    
                    // Handle Remember Me
                    if ($request->has('remember') && $request->remember) {
                        $rememberToken = Str::random(60);
                        $user->remember_token = Hash::make($rememberToken);
                        $user->save();
                        
                        $response->cookie(
                            'remember_token',
                            $rememberToken,
                            30 * 24 * 60, // 30 days in minutes
                            '/',
                            null,
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
                        '2fa_pending_remember' => $request->has('remember') && $request->remember,
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
                
                // Handle Remember Me
                $response = null;
                if ($user->role === 'nurse') {
                    $response = redirect()->intended(route('admin.health-reports'));
                } else {
                    $response = redirect()->intended(route('admin.dashboard'));
                }
                
                // Set remember token if remember me is checked
                if ($request->has('remember') && $request->remember) {
                    $rememberToken = Str::random(60);
                    $user->remember_token = Hash::make($rememberToken);
                    $user->save();
                    
                    $response->cookie(
                        'remember_token',
                        $rememberToken,
                        30 * 24 * 60, // 30 days in minutes
                        '/',
                        null,
                        false, // secure (false for local HTTP)
                        true, // httpOnly
                        false, // raw
                        'lax' // sameSite
                    );
                }
                
                return $response;
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
                    
                    // Handle Remember Me
                    if ($request->has('remember') && $request->remember) {
                        $rememberToken = Str::random(60);
                        $user->remember_token = Hash::make($rememberToken);
                        $user->save();
                        
                        $response->cookie(
                            'remember_token',
                            $rememberToken,
                            30 * 24 * 60, // 30 days in minutes
                            '/',
                            null,
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
                        '2fa_pending_remember' => $request->has('remember') && $request->remember,
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
                
                // Handle Remember Me
                $response = redirect()->intended(route('resident.dashboard'));
                
                // Set remember token if remember me is checked
                if ($request->has('remember') && $request->remember) {
                    $rememberToken = Str::random(60);
                    $user->remember_token = Hash::make($rememberToken);
                    $user->save();
                    
                    $response->cookie(
                        'remember_token',
                        $rememberToken,
                        30 * 24 * 60, // 30 days in minutes
                        '/',
                        null,
                        false, // secure (false for local HTTP)
                        true, // httpOnly
                        false, // raw
                        'lax' // sameSite
                    );
                }
                
                return $response;
            }
        }

        notify()->error('The provided credentials do not match our records.');
        return back()->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Clear remember token if exists
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userId) {
            if ($userRole === 'resident') {
                $user = Residents::find($userId);
            } else {
                $user = BarangayProfile::find($userId);
            }
            
            if ($user) {
                $user->remember_token = null;
                $user->save();
            }
        }
        
        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear remember token cookie
        return redirect('/')->cookie('remember_token', null, -1);
    }
}
