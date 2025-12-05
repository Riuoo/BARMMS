# Device Trust - Database Solution

## Problem with Cache-Based Approach

The previous cache-based approach had issues:
- Cache can be cleared unexpectedly
- Cache might not persist properly in local development
- Cache expiration can be unpredictable
- Different cache drivers behave differently

## New Database-Based Solution

### Changes Made

1. **Database Table**: `trusted_devices` table stores trusted devices
   - `user_id` - Foreign key to barangay_profiles
   - `device_identifier` - Unique device ID (uses fingerprint directly)
   - `device_fingerprint` - The persistent cookie fingerprint
   - `user_agent` - Browser user agent (for reference)
   - `expires_at` - When the trust expires (30 days)
   - `last_used_at` - Last time device was used

2. **Simplified Device ID**: 
   - Now uses fingerprint directly as device ID
   - No more hashing with user agent
   - More reliable and consistent

3. **Database Persistence**:
   - Trusted devices stored in database
   - Automatically cleaned up when expired
   - Last used timestamp updated on each use

### How It Works

1. **First Login with "Remember Device"**:
   - Generate unique fingerprint (32 random chars)
   - Store fingerprint in session before 2FA verification
   - After verification, store in database as trusted device
   - Set cookie with fingerprint (30 days)

2. **Subsequent Logins**:
   - Read fingerprint from cookie
   - Use fingerprint as device ID
   - Check database for trusted device
   - If found and not expired: Skip 2FA
   - If not found or expired: Require 2FA

3. **Database Cleanup**:
   - Expired devices automatically removed on check
   - Last used timestamp updated on each successful login

### Benefits

✅ **Reliable Persistence**: Database is more reliable than cache  
✅ **Simpler Logic**: Direct fingerprint usage, no complex hashing  
✅ **Better Tracking**: Can see all trusted devices in database  
✅ **Automatic Cleanup**: Expired devices removed automatically  
✅ **Works in Local Dev**: No cache driver issues  

### Testing

1. **Enable 2FA** for an admin account
2. **Login** and check "Remember this device"
3. **Check Database**: 
   ```sql
   SELECT * FROM trusted_devices WHERE user_id = [your_user_id];
   ```
4. **Check Cookie**: Look for `device_fingerprint` in browser DevTools
5. **Logout and Login Again**: Should skip 2FA
6. **Verify in Database**: `last_used_at` should be updated

### Database Schema

```sql
CREATE TABLE trusted_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    device_identifier VARCHAR(64) NOT NULL,
    device_fingerprint VARCHAR(64) NULL,
    user_agent VARCHAR(255) NULL,
    expires_at TIMESTAMP NOT NULL,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY (user_id, device_identifier),
    FOREIGN KEY (user_id) REFERENCES barangay_profiles(id) ON DELETE CASCADE
);
```

### Troubleshooting

**Device not trusted after login**:
1. Check if cookie exists: `device_fingerprint` in browser
2. Check database: `SELECT * FROM trusted_devices WHERE user_id = X`
3. Check expiration: `expires_at > NOW()`
4. Check device ID matches: Compare cookie value with `device_identifier`

**Cookie not persisting**:
1. Check browser cookie settings
2. Check if cookie is being set in response headers
3. Verify cookie path and domain settings
4. Try different browser

**Database entry not created**:
1. Check migration ran: `php artisan migrate:status`
2. Check for errors in logs
3. Verify database connection
4. Check foreign key constraint

