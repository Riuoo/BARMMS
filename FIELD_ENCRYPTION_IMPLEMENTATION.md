# Field-Level Encryption Implementation

## Overview
This document describes the implementation of field-level encryption for sensitive resident data in the BARMMS system.

## Encrypted Fields

The following sensitive fields in the `Residents` model are now encrypted:

1. **contact_number** - Resident's contact number
2. **birth_date** - Resident's date of birth (handled with custom accessors/mutators)
3. **emergency_contact_name** - Emergency contact's name
4. **emergency_contact_number** - Emergency contact's phone number
5. **emergency_contact_relationship** - Relationship to emergency contact

## Implementation Details

### Model Changes (`app/Models/Residents.php`)

1. **Encrypted Casts**: Added `encrypted` cast for string fields:
   - `contact_number`
   - `emergency_contact_name`
   - `emergency_contact_number`
   - `emergency_contact_relationship`

2. **Custom Date Encryption**: `birth_date` uses custom accessors and mutators because Laravel's `encrypted` cast doesn't support nested date casting:
   - `getBirthDateAttribute()` - Decrypts and returns Carbon date instance
   - `setBirthDateAttribute()` - Encrypts date string before storage
   - Handles both encrypted and unencrypted data (for backward compatibility)

### Encryption Command

Created `app/Console/Commands/EncryptResidentDataCommand.php` to encrypt existing data:

**Usage:**
```bash
# Dry run to see what would be encrypted
php artisan residents:encrypt-data --dry-run

# Actually encrypt the data
php artisan residents:encrypt-data

# Force re-encryption (even if already encrypted)
php artisan residents:encrypt-data --force
```

**Features:**
- Detects if data is already encrypted
- Skips already encrypted records (unless `--force` is used)
- Provides progress bar and summary
- Safe dry-run mode

## How It Works

### Automatic Encryption/Decryption

When using the Eloquent model, encryption and decryption happen automatically:

```php
// Reading - automatically decrypted
$resident = Residents::find(1);
$contact = $resident->contact_number; // Automatically decrypted

// Writing - automatically encrypted
$resident->contact_number = '09123456789';
$resident->save(); // Automatically encrypted before saving
```

### Backward Compatibility

The implementation handles both encrypted and unencrypted data:
- When reading: Tries to decrypt first, falls back to parsing as plain text if decryption fails
- When writing: Always encrypts new values
- Migration command can encrypt existing unencrypted data

## Important Notes

### Database Queries

⚠️ **Warning**: Encrypted fields cannot be used in WHERE clauses or LIKE searches directly. The database stores encrypted values, so:

```php
// ❌ This won't work (searches encrypted value, not plain text)
Residents::where('contact_number', 'like', '%1234%')->get();

// ✅ Use Eloquent accessors instead (loads all, filters in memory - not efficient for large datasets)
Residents::all()->filter(function($resident) {
    return str_contains($resident->contact_number, '1234');
});
```

**Current Status**: The codebase doesn't use WHERE clauses on encrypted fields, so no changes were needed.

### Performance Considerations

- Encryption/decryption adds minimal overhead
- All encrypted fields are automatically handled by Laravel
- No manual encryption/decryption needed in controllers

### Security

- Uses Laravel's built-in encryption (AES-256-CBC)
- Encryption key stored in `APP_KEY` environment variable
- **Important**: Keep `APP_KEY` secure and backed up - losing it means losing access to encrypted data

## Migration Steps

1. **Backup your database** before running the encryption command
2. Run the encryption command:
   ```bash
   php artisan residents:encrypt-data --dry-run  # Check first
   php artisan residents:encrypt-data             # Apply encryption
   ```
3. Test that data can be read correctly
4. Verify that new records are automatically encrypted

## Testing

To verify encryption is working:

```php
// Create a new resident
$resident = Residents::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'contact_number' => '09123456789',
    // ... other fields
]);

// Check database - contact_number should be encrypted
$raw = DB::table('residents')->where('id', $resident->id)->first();
// $raw->contact_number should be an encrypted string (starts with "eyJpdiI6")

// Read through model - should be decrypted
$resident = Residents::find($resident->id);
// $resident->contact_number should be '09123456789'
```

## Future Enhancements

Potential improvements:
1. Add searchable hash columns for encrypted fields (if search is needed)
2. Implement field-level access control (who can view encrypted fields)
3. Add audit logging for access to encrypted fields
4. Consider database-level encryption for additional security

## Troubleshooting

### Issue: "Decrypt error" when reading data
**Solution**: Data might be unencrypted. Run the encryption command to encrypt existing data.

### Issue: Can't search by contact number
**Solution**: This is expected - encrypted fields can't be searched directly. Consider adding a searchable hash column if needed.

### Issue: Lost APP_KEY
**Solution**: Without the encryption key, encrypted data cannot be decrypted. Always backup your `.env` file and `APP_KEY`.

