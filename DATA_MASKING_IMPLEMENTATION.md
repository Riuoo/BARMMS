# Data Masking/Redaction Implementation

## Overview
This document describes the implementation of data masking/redaction for sensitive resident information in the BARMMS system, as per recommendation 1.3.

## Implementation Summary

### 1. DataMasking Helper Class (`app/Helpers/DataMasking.php`)

A comprehensive helper class that provides masking functionality:

- **`maskPhoneNumber()`** - Masks phone numbers showing only last N digits (default: 4)
  - Example: `09123456789` → `****-****-6789`
  
- **`maskName()`** - Masks names showing first letter of each part
  - Example: `Juan Dela Cruz` → `J*** D*** C***`
  
- **`maskString()`** - Generic string masking with configurable visible characters
  
- **`getMaskingLevel()`** - Returns masking level based on user role:
  - `'none'` - No masking (admin, secretary)
  - `'partial'` - Partial masking (nurse, captain, councilor)
  - `'full'` - Full masking (treasurer, other roles)

### 2. Residents Model Methods

Added masking methods to the `Residents` model that automatically check user roles:

- **`getMaskedContactNumber($userRole = null)`** - Returns masked contact number
- **`getMaskedEmergencyContactName($userRole = null)`** - Returns masked emergency contact name
- **`getMaskedEmergencyContactNumber($userRole = null)`** - Returns masked emergency contact number
- **`getMaskedEmergencyContactRelationship($userRole = null)`** - Returns masked relationship

**Special Handling:**
- Residents viewing their own data see unmasked values
- Role-based masking is applied automatically based on session role
- Methods can accept optional role parameter for testing or specific use cases

### 3. Role-Based Masking Levels

#### Full Access (No Masking)
- **Admin** - Can see all data unmasked
- **Secretary** - Can see all data unmasked

#### Partial Access (Partial Masking)
- **Nurse** - Can see partial data (last 4 digits of phone, first letter of names)
- **Captain** - Can see partial data
- **Councilor** - Can see partial data
- **Resident** - Can see their own data unmasked, others' data partially masked

#### Restricted Access (Full Masking)
- **Treasurer** - Sees fully masked data (last 2 digits only)
- **Other roles** - Default to full masking

### 4. Updated Views

The following views have been updated to use masked data:

1. **Admin Residents List** (`resources/views/admin/residents/residents.blade.php`)
   - Contact numbers are masked based on user role
   - Demographics modal shows masked data

2. **Resident Profile** (`resources/views/resident/profile.blade.php`)
   - Residents see their own data unmasked
   - Emergency contact information is masked for other users

3. **Demographics Modal** (via AJAX)
   - Returns masked data through the controller

### 5. Updated Controllers

- **`ResidentController::getDemographics()`** - Returns masked sensitive data
- **`ResidentController::summary()`** - Returns masked contact number

## Usage Examples

### In Blade Templates

```blade
<!-- Contact number with automatic masking -->
{{ $resident->getMaskedContactNumber() }}

<!-- Emergency contact with automatic masking -->
{{ $resident->getMaskedEmergencyContactName() }}
{{ $resident->getMaskedEmergencyContactNumber() }}
{{ $resident->getMaskedEmergencyContactRelationship() }}
```

### In Controllers

```php
// Automatic masking based on current user's role
$maskedContact = $resident->getMaskedContactNumber();

// Override role for specific use case
$maskedContact = $resident->getMaskedContactNumber('nurse');
```

### Direct Helper Usage

```php
use App\Helpers\DataMasking;

// Mask phone number
$masked = DataMasking::maskPhoneNumber('09123456789', 4); // ****-****-6789

// Mask name
$masked = DataMasking::maskName('Juan Dela Cruz'); // J*** D*** C***

// Check masking level
$level = DataMasking::getMaskingLevel('nurse'); // 'partial'
```

## Export Masking

For future implementation, when exporting resident data:

```php
// In export methods, apply masking based on user role
$exportData = [];
foreach ($residents as $resident) {
    $exportData[] = [
        'name' => $resident->name,
        'contact_number' => $resident->getMaskedContactNumber(),
        'emergency_contact' => $resident->getMaskedEmergencyContactName(),
        // ... other fields
    ];
}
```

## Security Considerations

1. **Automatic Role Detection**: Masking methods automatically detect user role from session
2. **Own Data Access**: Residents can see their own data unmasked
3. **Consistent Application**: All sensitive fields use the same masking logic
4. **No Bypass**: Masking is applied at the model level, making it difficult to bypass

## Testing

To test masking functionality:

1. **Login as different roles** and view resident data
2. **Check that masking levels** match role permissions
3. **Verify residents** can see their own data unmasked
4. **Test demographics modal** shows masked data

## Future Enhancements

1. **Export Masking**: Apply masking to CSV/Excel exports
2. **PDF Report Masking**: Mask sensitive data in PDF reports
3. **Audit Logging**: Log when masked data is viewed
4. **Custom Masking Rules**: Allow configuration of masking levels per role
5. **Reveal on Click**: Option to reveal masked data with proper authorization

## Files Modified

1. `app/Helpers/DataMasking.php` - New helper class
2. `app/Models/Residents.php` - Added masking methods
3. `resources/views/admin/residents/residents.blade.php` - Updated to use masked data
4. `resources/views/resident/profile.blade.php` - Updated to use masked data
5. `app/Http/Controllers/AdminControllers/UserManagementControllers/ResidentController.php` - Updated to return masked data

## Notes

- Masking is applied automatically - no manual intervention needed
- Encryption (from 1.1) and masking (1.3) work together for comprehensive data protection
- Masking does not affect database storage - data is still encrypted at rest
- Masking only affects display - actual data remains encrypted in the database

