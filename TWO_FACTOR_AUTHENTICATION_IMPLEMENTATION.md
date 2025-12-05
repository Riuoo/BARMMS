# Two-Factor Authentication (2FA) Implementation

## Overview
This document describes the implementation of Two-Factor Authentication (2FA) for admin accounts in the BARMMS system, as per recommendation 2.2.

## Implementation Summary

### 1. Package Installation
- **Package**: `pragmarx/google2fa` (added to `composer.json`)
- **Status**: Package needs to be installed via `composer install` or `composer require pragmarx/google2fa`
- **Note**: If the GD extension is missing, install it first or use `--ignore-platform-req=ext-gd` flag

### 2. Database Migration
- **File**: `database/migrations/2025_12_05_105526_add_two_factor_authentication_to_barangay_profiles_table.php`
- **Fields Added**:
  - `two_factor_secret` (text, nullable) - Stores the encrypted secret key
  - `two_factor_enabled` (boolean, default: false) - Indicates if 2FA is enabled
  - `two_factor_enabled_at` (timestamp, nullable) - When 2FA was enabled

### 3. Model Updates
- **File**: `app/Models/BarangayProfile.php`
- **Added**:
  - Fields to `$fillable` array
  - Casts for `two_factor_enabled` (boolean) and `two_factor_enabled_at` (datetime)
  - `hasTwoFactorEnabled()` method to check if 2FA is active

### 4. Service Layer
- **File**: `app/Services/TwoFactorAuthService.php`
- **Key Methods**:
  - `generateSecretKey()` - Generates a new 2FA secret
  - `getQRCodeUrl()` - Generates QR code URL for authenticator apps
  - `verifyCode()` - Verifies a 6-digit code from authenticator app
  - `isDeviceTrusted()` - Checks if device is remembered
  - `trustDevice()` - Remembers device for specified days (default: 30)
  - `revokeDeviceTrust()` - Removes device trust
  - `isRequiredForOperation()` - Checks if operation requires 2FA
  - `markOperationVerified()` - Marks operation as verified (15-minute cache)

### 5. Controller
- **File**: `app/Http/Controllers/Auth/TwoFactorAuthController.php`
- **Routes**:
  - `GET /2fa/setup` - Show 2FA setup page
  - `POST /2fa/enable` - Enable 2FA after verification
  - `GET /2fa/verify` - Show 2FA verification page (for login)
  - `POST /2fa/verify` - Verify 2FA code during login
  - `GET /2fa/verify-operation` - Show 2FA verification for sensitive operations
  - `POST /2fa/verify-operation` - Verify 2FA for sensitive operations
  - `POST /2fa/disable` - Disable 2FA (requires password)

### 6. Middleware
- **File**: `app/Http/Middleware/RequireTwoFactorAuth.php`
- **Registered as**: `2fa` in `bootstrap/app.php`
- **Functionality**:
  - Checks if user has 2FA enabled
  - Checks if device is trusted (remembered)
  - Checks if operation was recently verified
  - Redirects to 2FA verification if needed

### 7. Login Flow Updates
- **File**: `app/Http/Controllers/Auth/LoginController.php`
- **Changes**:
  - After password verification, checks if 2FA is enabled
  - If enabled and device not trusted, redirects to 2FA verification
  - If device is trusted, completes login normally
  - Supports "remember device" option (30 days)

### 8. Routes
- **Sensitive Routes Protected**:
  - `/admin/residents/{resident}/demographics` - View demographics (requires `view_demographics` operation)
  - `/admin/residents/{id}/edit` - Edit resident (requires `edit_resident` operation)
  - `/admin/residents/{id}` (DELETE) - Delete resident (requires `delete_resident` operation)

### 9. Views
- **Setup View**: `resources/views/auth/two-factor/setup.blade.php`
  - QR code display
  - Manual secret entry option
  - Verification code input
  
- **Login Verification**: `resources/views/auth/two-factor/verify.blade.php`
  - 6-digit code input
  - "Remember device" checkbox
  
- **Operation Verification**: `resources/views/auth/two-factor/verify-operation.blade.php`
  - 6-digit code input for sensitive operations
  - 15-minute verification cache

- **Profile Integration**: `resources/views/admin/barangay-profiles/profile.blade.php`
  - 2FA status display
  - Enable/Disable 2FA section
  - Link to setup page

## How It Works

### Initial Setup Flow
1. User navigates to Profile → Enable 2FA
2. System generates secret key and QR code
3. User scans QR code with authenticator app
4. User enters verification code
5. System enables 2FA and stores secret

### Login Flow (2FA Enabled)
1. User enters email and password
2. System checks if 2FA is enabled
3. If enabled:
   - Check if device is trusted (remembered)
   - If trusted: Complete login
   - If not trusted: Show 2FA verification page
4. User enters 6-digit code from authenticator app
5. Optional: User checks "Remember this device"
6. System completes login

### Sensitive Operations Flow
1. User attempts sensitive operation (e.g., view demographics)
2. Middleware checks if operation requires 2FA
3. If required and not recently verified:
   - Redirect to 2FA verification page
   - User enters 6-digit code
   - System marks operation as verified (15 minutes)
4. User is redirected back to original operation
5. Operation proceeds normally

## Security Features

### Device Trust
- Users can "remember this device" for 30 days
- Trusted devices skip 2FA on login
- Device ID is generated from user agent + IP address
- Trust can be revoked (all devices when 2FA is disabled)

### Operation Verification Cache
- Sensitive operations verified once are cached for 15 minutes
- Prevents repeated 2FA prompts for same operation
- Cache is per-user and per-operation

### Time Window
- 2FA codes are valid for 2 time periods (60 seconds) to account for clock skew
- Standard TOTP (Time-based One-Time Password) algorithm

## Usage Instructions

### For Administrators

#### Enabling 2FA
1. Go to Profile page (`/admin/profile`)
2. Scroll to "Two-Factor Authentication" section
3. Click "Enable Two-Factor Authentication"
4. Scan QR code with authenticator app (Google Authenticator, Authy, etc.)
5. Enter 6-digit code to verify
6. 2FA is now enabled

#### Disabling 2FA
1. Go to Profile page
2. Scroll to "Two-Factor Authentication" section
3. Enter your password
4. Click "Disable Two-Factor Authentication"

#### During Login
1. Enter email and password
2. If 2FA is enabled and device not trusted:
   - Enter 6-digit code from authenticator app
   - Optionally check "Remember this device"
3. Click "Verify & Continue"

#### Sensitive Operations
- Viewing resident demographics
- Editing resident information
- Deleting residents
- These operations will prompt for 2FA if not verified in last 15 minutes

## Installation Steps

1. **Install Package**:
   ```bash
   composer require pragmarx/google2fa
   ```

2. **Run Migration**:
   ```bash
   php artisan migrate
   ```

3. **Clear Cache** (if needed):
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## Configuration

### Device Trust Duration
- Default: 30 days
- Can be changed in `TwoFactorAuthService::trustDevice()` method

### Operation Verification Cache
- Default: 15 minutes
- Can be changed in `TwoFactorAuthService::markOperationVerified()` method

### Time Window for Code Verification
- Default: 2 periods (60 seconds)
- Can be changed in `TwoFactorAuthService::verifyCode()` method

## Supported Authenticator Apps

- Google Authenticator
- Microsoft Authenticator
- Authy
- Any TOTP-compatible authenticator app

## Security Considerations

1. **Secret Storage**: Secrets are stored in database (consider encryption for production)
2. **Device Trust**: Based on user agent + IP (can be spoofed, but provides convenience)
3. **Backup Codes**: Not implemented yet (future enhancement)
4. **Recovery**: No recovery mechanism yet (admin must disable 2FA manually)

## Future Enhancements

1. **Backup Codes**: Generate recovery codes when enabling 2FA
2. **SMS 2FA**: Alternative to authenticator app
3. **Email 2FA**: Alternative verification method
4. **Device Management**: View and revoke trusted devices
5. **2FA History**: Log 2FA verification attempts
6. **Admin Override**: Allow admins to disable 2FA for users (emergency)

## Troubleshooting

### QR Code Not Displaying
- Check if `simplesoftwareio/simple-qrcode` package is installed
- Check if GD extension is enabled in PHP
- Fallback: Manual secret entry is available

### Code Not Working
- Ensure device time is synchronized
- Check if code is entered within 60-second window
- Verify secret was scanned correctly

### Device Not Remembered
- Clear browser cookies/cache
- Device ID changes if IP or user agent changes
- Re-check "Remember this device" on next login

## Files Modified/Created

### Created
- `app/Services/TwoFactorAuthService.php`
- `app/Http/Controllers/Auth/TwoFactorAuthController.php`
- `app/Http/Middleware/RequireTwoFactorAuth.php`
- `database/migrations/2025_12_05_105526_add_two_factor_authentication_to_barangay_profiles_table.php`
- `resources/views/auth/two-factor/setup.blade.php`
- `resources/views/auth/two-factor/verify.blade.php`
- `resources/views/auth/two-factor/verify-operation.blade.php`

### Modified
- `app/Models/BarangayProfile.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `bootstrap/app.php`
- `routes/web.php`
- `composer.json`
- `resources/views/admin/barangay-profiles/profile.blade.php`

## Testing Checklist

- [ ] Enable 2FA for admin account
- [ ] Login with 2FA enabled (new device)
- [ ] Login with 2FA enabled (trusted device)
- [ ] Access sensitive operation (view demographics)
- [ ] Verify 2FA is required for sensitive operations
- [ ] Disable 2FA
- [ ] Test with multiple authenticator apps
- [ ] Test device trust expiration
- [ ] Test operation verification cache

