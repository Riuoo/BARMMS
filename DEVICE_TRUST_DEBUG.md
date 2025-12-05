# Device Trust Debugging Guide

## Common Issues in Local Development

### 1. Cookie Not Being Set
**Symptoms**: Device fingerprint cookie doesn't appear in browser
**Causes**:
- Cookie encryption middleware might be encrypting it
- Cookie path/domain mismatch
- Browser blocking cookies
- HTTPS requirement (secure flag)

**Solution**: 
- Check browser DevTools → Application → Cookies
- Verify cookie is being set in response headers
- Check if cookie encryption is interfering

### 2. Cookie Not Being Read
**Symptoms**: Cookie exists but `$request->cookie('device_fingerprint')` returns null
**Causes**:
- Cookie encryption (encrypted cookies need to be decrypted)
- Cookie name mismatch
- Domain/path mismatch

**Solution**:
- Add cookie to encryption exception list if using EncryptCookies middleware
- Check cookie name matches exactly
- Verify domain and path settings

### 3. Device ID Mismatch
**Symptoms**: Device is trusted in cache but not recognized
**Causes**:
- Different fingerprint used to generate device ID
- User agent changed
- Cookie not persisting between requests

**Solution**:
- Ensure same fingerprint is used throughout
- Check user agent consistency
- Verify cookie persistence

## Debugging Steps

### Step 1: Check if Cookie is Set
```php
// In TwoFactorAuthController after setting cookie
\Log::info('Cookie set', [
    'fingerprint' => $deviceFingerprint,
    'cookie_exists' => $request->cookie('device_fingerprint') !== null
]);
```

### Step 2: Check Cache Entry
```php
// In LoginController when checking device trust
$cacheKey = "2fa_trusted_device_{$user->id}_{$deviceId}";
\Log::info('Device trust check', [
    'device_id' => $deviceId,
    'cache_key' => $cacheKey,
    'is_trusted' => Cache::has($cacheKey),
    'cache_value' => Cache::get($cacheKey)
]);
```

### Step 3: Check Device ID Generation
```php
// In generateDeviceId method
\Log::info('Device ID generation', [
    'user_agent' => $userAgent,
    'fingerprint' => $fingerprint,
    'device_id' => hash('sha256', $userAgent . $fingerprint)
]);
```

## Testing Checklist

1. **First Login with "Remember Device"**:
   - [ ] Cookie is set in response
   - [ ] Cookie appears in browser DevTools
   - [ ] Cache entry is created
   - [ ] Device ID is consistent

2. **Second Login (Should Skip 2FA)**:
   - [ ] Cookie is read from request
   - [ ] Device ID matches cached device ID
   - [ ] Device is recognized as trusted
   - [ ] 2FA is skipped

3. **After Clearing Cookies**:
   - [ ] New fingerprint is generated
   - [ ] Device is not trusted
   - [ ] 2FA is required

## Quick Fixes for Local Development

### Option 1: Disable Cookie Encryption (Temporary)
If using EncryptCookies middleware, add exception:
```php
// In bootstrap/app.php or middleware
$middleware->validateCsrfTokens(except: [
    // ...
]);

// Or create EncryptCookies middleware override
protected $except = [
    'device_fingerprint',
];
```

### Option 2: Use Session Instead of Cookie (Temporary)
For local testing, you could store fingerprint in session:
```php
// Instead of cookie, use session
Session::put('device_fingerprint', $fingerprint);
$fingerprint = Session::get('device_fingerprint');
```

### Option 3: Add Debug Logging
Add logging to trace the flow:
```php
\Log::info('2FA Device Trust Flow', [
    'step' => 'login_check',
    'user_id' => $user->id,
    'device_id' => $deviceId,
    'cookie_exists' => $request->cookie('device_fingerprint') !== null,
    'is_trusted' => $this->twoFactorService->isDeviceTrusted($user->id, $deviceId)
]);
```

## Browser DevTools Checks

1. **Application Tab → Cookies**:
   - Check if `device_fingerprint` cookie exists
   - Verify expiration date (should be 30 days)
   - Check path and domain

2. **Network Tab → Request Headers**:
   - Check if cookie is sent in Cookie header
   - Format: `Cookie: device_fingerprint=abc123...`

3. **Network Tab → Response Headers**:
   - Check if Set-Cookie header is present
   - Format: `Set-Cookie: device_fingerprint=abc123...; expires=...`

## Common Local Development Issues

### Issue: Cookie Not Persisting
**Cause**: Browser privacy settings, incognito mode, or cookie blocking
**Fix**: 
- Use regular (non-incognito) browser window
- Check browser cookie settings
- Clear cookies and try again

### Issue: Different Device ID Each Time
**Cause**: Fingerprint regenerated each time
**Fix**: 
- Ensure cookie is set and persisted
- Check that same fingerprint is used consistently
- Verify cookie is being read correctly

### Issue: Cache Not Persisting
**Cause**: Cache driver issues or cache clearing
**Fix**:
- Check cache driver (database/file)
- Verify cache table exists
- Check cache configuration

