<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\TrustedDevice;

class TwoFactorAuthService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a secret key for 2FA setup
     * 
     * @return string
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Get QR code URL for 2FA setup
     * 
     * @param string $email
     * @param string $secret
     * @param string $issuer
     * @return string
     */
    public function getQRCodeUrl(string $email, string $secret, string $issuer = 'BARMMS'): string
    {
        return $this->google2fa->getQRCodeUrl(
            $issuer,
            $email,
            $secret
        );
    }

    /**
     * Verify 2FA code
     * 
     * @param string|null $secret
     * @param string $code
     * @return bool
     */
    public function verifyCode(?string $secret, string $code): bool
    {
        if (empty($secret) || empty($code)) {
            return false;
        }

        // Allow a time window of 2 periods (60 seconds) for clock skew
        $valid = $this->google2fa->verifyKey($secret, $code, 2);
        
        return $valid;
    }

    /**
     * Check if device is trusted (remembered)
     * Uses database for reliable persistence
     * 
     * @param int $userId
     * @param string $deviceId
     * @return bool
     */
    public function isDeviceTrusted(int $userId, string $deviceId): bool
    {
        // Clean up expired devices first
        TrustedDevice::where('user_id', $userId)
            ->where('expires_at', '<', now())
            ->delete();
        
        $trustedDevice = TrustedDevice::where('user_id', $userId)
            ->where('device_identifier', $deviceId)
            ->where('expires_at', '>', now())
            ->first();
        
        if ($trustedDevice) {
            // Update last used timestamp
            $trustedDevice->update(['last_used_at' => now()]);
            return true;
        }
        
        return false;
    }

    /**
     * Trust a device (remember for specified days)
     * Stores in database for reliable persistence
     * 
     * @param int $userId
     * @param string $deviceId
     * @param string|null $deviceFingerprint
     * @param string|null $userAgent
     * @param int $days
     * @return void
     */
    public function trustDevice(int $userId, string $deviceId, ?string $deviceFingerprint = null, ?string $userAgent = null, int $days = 30): void
    {
        // If fingerprint not provided, use deviceId as fingerprint
        if (!$deviceFingerprint) {
            $deviceFingerprint = $deviceId;
        }
        
        // Remove any expired entries first
        TrustedDevice::where('user_id', $userId)
            ->where('expires_at', '<=', now())
            ->delete();
        
        // Create or update trusted device
        TrustedDevice::updateOrCreate(
            [
                'user_id' => $userId,
                'device_identifier' => $deviceId,
            ],
            [
                'device_fingerprint' => $deviceFingerprint,
                'user_agent' => $userAgent,
                'expires_at' => now()->addDays($days),
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Revoke device trust
     * 
     * @param int $userId
     * @param string|null $deviceId If null, revokes all devices
     * @return void
     */
    public function revokeDeviceTrust(int $userId, ?string $deviceId = null): void
    {
        if ($deviceId) {
            TrustedDevice::where('user_id', $userId)
                ->where('device_identifier', $deviceId)
                ->delete();
        } else {
            // Revoke all devices for this user
            TrustedDevice::where('user_id', $userId)->delete();
        }
    }

    /**
     * Generate device ID from request
     * Uses persistent cookie fingerprint for reliable device identification
     * 
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function generateDeviceId(\Illuminate\Http\Request $request): string
    {
        // First, try to get fingerprint from cookie (most reliable)
        $fingerprint = $request->cookie('device_fingerprint');
        
        // If not in cookie, try to get from session (during verification flow)
        if (!$fingerprint) {
            $fingerprint = \Illuminate\Support\Facades\Session::get('device_fingerprint')
                ?? \Illuminate\Support\Facades\Session::get('2fa_pending_device_fingerprint');
        }
        
        // If still no fingerprint, generate new one
        if (!$fingerprint) {
            $fingerprint = Str::random(32);
        }
        
        // Use fingerprint directly as device ID (simpler and more reliable)
        // The fingerprint is already unique and persistent
        return $fingerprint;
    }

    /**
     * Get or generate device fingerprint
     * 
     * @param \Illuminate\Http\Request $request
     * @param string|null $storedFingerprint Optional stored fingerprint from session
     * @return string
     */
    public function getDeviceFingerprint(\Illuminate\Http\Request $request, ?string $storedFingerprint = null): string
    {
        // First, try to use stored fingerprint from session (if available)
        if ($storedFingerprint) {
            return $storedFingerprint;
        }
        
        // Then, try to get from cookie
        $fingerprint = $request->cookie('device_fingerprint');
        
        if (!$fingerprint) {
            // Generate new fingerprint only if not in cookie and not in session
            $fingerprint = Str::random(32);
        }
        
        return $fingerprint;
    }

    /**
     * Check if 2FA is required for sensitive operation
     * 
     * @param int $userId
     * @param string $operation
     * @return bool
     */
    public function isRequiredForOperation(int $userId, string $operation): bool
    {
        // Check if 2FA was recently verified for this operation
        $cacheKey = "2fa_verified_{$userId}_{$operation}";
        
        if (Cache::has($cacheKey)) {
            return false; // Already verified recently
        }

        // Sensitive operations that require 2FA
        $sensitiveOperations = [
            'view_sensitive_data',
            'edit_resident',
            'delete_resident',
            'export_data',
            'view_demographics',
            'view_income_level',
            'view_emergency_contact',
        ];

        return in_array($operation, $sensitiveOperations);
    }

    /**
     * Mark operation as 2FA verified (valid for 15 minutes)
     * 
     * @param int $userId
     * @param string $operation
     * @return void
     */
    public function markOperationVerified(int $userId, string $operation): void
    {
        $cacheKey = "2fa_verified_{$userId}_{$operation}";
        Cache::put($cacheKey, true, now()->addMinutes(15));
    }

    /**
     * Clear all 2FA verification cache for user
     * 
     * @param int $userId
     * @return void
     */
    public function clearVerificationCache(int $userId): void
    {
        // Clear all verification caches for this user
        // In production, you might want to track these more precisely
        Cache::tags(["2fa_verified_{$userId}"])->flush();
    }
}

