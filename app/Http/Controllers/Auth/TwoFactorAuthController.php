<?php

namespace App\Http\Controllers\Auth;

use App\Models\BarangayProfile;
use App\Models\Residents;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TwoFactorAuthController
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Get user based on role
     */
    protected function getUser()
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId) {
            return null;
        }

        if ($userRole === 'resident') {
            return Residents::find($userId);
        } else {
            return BarangayProfile::find($userId);
        }
    }

    /**
     * Get redirect route based on user role
     */
    protected function getDashboardRoute($userRole)
    {
        if ($userRole === 'resident') {
            return route('resident.dashboard');
        } elseif ($userRole === 'nurse') {
            return route('admin.health-reports');
        } else {
            return route('admin.dashboard');
        }
    }

    /**
     * Show 2FA setup page
     */
    public function showSetup()
    {
        $user = $this->getUser();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // If already enabled, redirect to dashboard
        if ($user->hasTwoFactorEnabled()) {
            $userRole = Session::get('user_role');
            return redirect($this->getDashboardRoute($userRole));
        }

        // Generate secret if not exists
        if (empty($user->two_factor_secret)) {
            $user->two_factor_secret = $this->twoFactorService->generateSecretKey();
            $user->save();
        }

        // Generate QR code URL
        $qrCodeUrl = $this->twoFactorService->getQRCodeUrl(
            $user->email,
            $user->two_factor_secret,
            'BARMMS'
        );

        // Generate QR code SVG
        // Note: Requires simplesoftwareio/simple-qrcode package
        // If package is not available, use the QR code URL directly
        try {
            if (class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrCodeUrl);
            } else {
                throw new \Exception('QR Code package not available');
            }
        } catch (\Exception $e) {
            // Fallback: return URL if QR code generation fails
            $qrCodeSvg = '<div class="text-center p-4"><p class="text-sm text-gray-600 mb-2">Scan this URL with your authenticator app:</p><p class="text-xs break-all font-mono">' . htmlspecialchars($qrCodeUrl) . '</p></div>';
        }

        $userRole = Session::get('user_role');
        $layout = ($userRole === 'resident') ? 'resident.layout' : 'admin.main.layout';
        $cancelRoute = ($userRole === 'resident') ? route('resident.dashboard') : route('admin.dashboard');
        
        return view('auth.two-factor.setup', [
            'user' => $user,
            'secret' => $user->two_factor_secret,
            'qrCodeUrl' => $qrCodeUrl,
            'qrCodeSvg' => $qrCodeSvg,
            'layout' => $layout,
            'cancelRoute' => $cancelRoute,
        ]);
    }

    /**
     * Verify and enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $this->getUser();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the raw secret (in case it's being cast/encrypted)
        $secret = $user->getRawOriginal('two_factor_secret') ?? $user->two_factor_secret;
        
        // Verify the code
        if (!$this->twoFactorService->verifyCode($secret, $request->code)) {
            notify()->error('Invalid verification code. Please try again.');
            return back()->withInput();
        }

        // Enable 2FA
        $user->two_factor_enabled = true;
        $user->two_factor_enabled_at = now();
        $user->save();

        notify()->success('Two-factor authentication has been enabled successfully.');
        $userRole = Session::get('user_role');
        return redirect($this->getDashboardRoute($userRole));
    }

    /**
     * Show 2FA verification page (for login)
     */
    public function showVerification()
    {
        // Check if user is in pending 2FA state
        if (!Session::has('2fa_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor.verify');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = Session::get('2fa_pending_user_id');
        $userRole = Session::get('2fa_pending_user_role');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        // Get user based on role
        if ($userRole === 'resident') {
            $user = Residents::findOrFail($userId);
        } else {
            $user = BarangayProfile::findOrFail($userId);
        }

        // Check if user has 2FA enabled and secret exists
        if (!$user->hasTwoFactorEnabled() || empty($user->two_factor_secret)) {
            notify()->error('Two-factor authentication is not enabled for this account.');
            Session::forget('2fa_pending_user_id');
            Session::forget('2fa_pending_user_role');
            Session::forget('2fa_pending_device_id');
            Session::forget('2fa_pending_device_fingerprint');
            return redirect()->route('login');
        }

        // Get the raw secret (in case it's being cast/encrypted)
        $secret = $user->getRawOriginal('two_factor_secret') ?? $user->two_factor_secret;
        
        // Debug logging (remove in production)
        Log::debug('2FA Verification', [
            'user_id' => $userId,
            'user_role' => $userRole,
            'has_secret' => !empty($secret),
            'secret_length' => strlen($secret ?? ''),
            'code' => $request->code,
        ]);
        
        // Verify the code
        if (!$this->twoFactorService->verifyCode($secret, $request->code)) {
            notify()->error('Invalid verification code. Please try again.');
            return back()->withInput();
        }

        // Check if remember device is checked (checkbox sends "on" when checked)
        $rememberDevice = $request->has('remember_device') && 
                         ($request->remember_device === 'on' || $request->remember_device === true || $request->remember_device === '1' || $request->remember_device === 1);

        // If remember device is checked, trust this device
        $deviceId = null;
        $deviceFingerprint = null;
        
        if ($rememberDevice) {
            // Get device fingerprint from session (stored before verification)
            $deviceFingerprint = Session::get('2fa_pending_device_fingerprint');
            
            // If not in session, get from cookie or generate new
            if (!$deviceFingerprint) {
                $deviceFingerprint = $this->twoFactorService->getDeviceFingerprint($request);
            }
            
            // Use fingerprint directly as device ID (simpler and more reliable)
            $deviceId = $deviceFingerprint;
            $userAgent = $request->userAgent() ?? '';
            
            // Trust this device in database
            try {
                $this->twoFactorService->trustDevice($userId, $deviceId, $deviceFingerprint, $userAgent, 30);
                
                // Store in session for future use
                Session::put('2fa_device_id', $deviceId);
                Session::put('device_fingerprint', $deviceFingerprint);
            } catch (\Exception $e) {
                // Log error but don't fail the login
                Log::error('Failed to trust device', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
                // Continue without device trust
                $deviceFingerprint = null;
            }
        }

        // Complete login
        Session::put('user_id', $userId);
        $userRole = Session::get('2fa_pending_user_role') ?? ($user->role ?? 'resident');
        Session::put('user_role', $userRole);
        Session::forget('2fa_pending_user_id');
        Session::forget('2fa_pending_user_role');
        Session::forget('2fa_pending_device_id');
        Session::forget('2fa_pending_device_fingerprint');

        notify()->success('Login successful.');

        // Prepare response with cookie if device is being remembered
        $response = null;
        $userRole = Session::get('2fa_pending_user_role') ?? $user->role;
        
        if ($userRole === 'resident') {
            $response = redirect()->intended(route('resident.dashboard'));
        } elseif ($userRole === 'nurse') {
            $response = redirect()->intended(route('admin.health-reports'));
        } else {
            $response = redirect()->intended(route('admin.dashboard'));
        }
        
        // Set device fingerprint cookie if device is being remembered
        if ($deviceFingerprint) {
            try {
                // Create cookie using Cookie facade for better compatibility
                $cookie = \Illuminate\Support\Facades\Cookie::make(
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
                $response->cookie($cookie);
            } catch (\Exception $e) {
                // Log error but don't fail the login
                Log::error('Failed to set device fingerprint cookie', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return $response;
    }

    /**
     * Show 2FA verification page for sensitive operations
     */
    public function showOperationVerification(Request $request)
    {
        $operation = $request->get('operation', 'sensitive_operation');
        $redirectTo = $request->get('redirect', route('admin.dashboard'));

        return view('auth.two-factor.verify-operation', [
            'operation' => $operation,
            'redirectTo' => $redirectTo,
        ]);
    }

    /**
     * Verify 2FA code for sensitive operations
     */
    public function verifyOperation(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'operation' => 'required|string',
            'redirect' => 'nullable|string',
        ]);

        $user = $this->getUser();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $userId = $user->id;

        // Check if user has 2FA enabled and secret exists
        if (!$user->hasTwoFactorEnabled() || empty($user->two_factor_secret)) {
            notify()->error('Two-factor authentication is not enabled for this account.');
            $userRole = Session::get('user_role');
            return redirect($this->getDashboardRoute($userRole));
        }

        // Get the raw secret (in case it's being cast/encrypted)
        $secret = $user->getRawOriginal('two_factor_secret') ?? $user->two_factor_secret;
        
        // Verify the code
        if (!$this->twoFactorService->verifyCode($secret, $request->code)) {
            notify()->error('Invalid verification code. Please try again.');
            return back()->withInput();
        }

        // Mark operation as verified
        $this->twoFactorService->markOperationVerified($userId, $request->operation);

        notify()->success('Verification successful.');

        // Check if there's a pending delete operation
        $pendingDelete = Session::get('pending_delete_operation');
        if ($pendingDelete && $pendingDelete['operation'] === $request->operation) {
            Session::forget('pending_delete_operation');
            
            // Redirect to a special route that will handle the delete
            return redirect()->route('admin.residents.delete.confirm', [
                'id' => $pendingDelete['route_params']['id'] ?? $pendingDelete['route_params']['resident'] ?? null
            ]);
        }

        $userRole = Session::get('user_role');
        $defaultRoute = $this->getDashboardRoute($userRole);
        $redirectTo = $request->redirect ?? $defaultRoute;
        return redirect($redirectTo);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $this->getUser();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $userId = $user->id;

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            notify()->error('Invalid password.');
            return back()->withInput();
        }

        // Disable 2FA
        $user->two_factor_enabled = false;
        $user->two_factor_enabled_at = null;
        $user->two_factor_secret = null;
        $user->save();

        // Revoke all trusted devices
        $this->twoFactorService->revokeDeviceTrust($userId);

        notify()->success('Two-factor authentication has been disabled.');
        $userRole = Session::get('user_role');
        return redirect($this->getDashboardRoute($userRole));
    }
}
