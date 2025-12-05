# Field-Level Access Control Implementation

## Overview
This document describes the implementation of field-level access control (recommendation 2.1) for resident information in the BARMMS system. This provides granular permissions to control which roles can view which specific fields.

## Implementation Summary

### 1. FieldPermission Helper Class (`app/Helpers/FieldPermission.php`)

A comprehensive permission system that defines field-level access:

**Key Features:**
- **Permission Matrix**: Defines which roles can view which fields
- **Field Categories**: Organizes fields into logical groups (basic, contact, demographic, health, system)
- **Role-Based Checks**: Methods to check field visibility based on user roles
- **Data Filtering**: Methods to filter arrays based on permissions

**Permission Matrix Structure:**
```php
'field_name' => ['allowed_roles']
```

**Example Permissions:**
- `income_level` → Only `admin`, `secretary` (highly sensitive)
- `emergency_contact_*` → `admin`, `secretary`, `nurse` (health-related access)
- `occupation` → `admin`, `secretary`, `captain`, `councilor` (not nurse)
- `education_level` → `admin`, `secretary`, `captain`, `councilor` (not nurse)

### 2. Residents Model Methods

Added field visibility methods to the `Residents` model:

- **`canViewField($fieldName, $userRole = null)`** - Check if current user can view a field
- **`getViewableFields($userRole = null)`** - Get all fields user can view
- **`getHiddenFields($userRole = null)`** - Get all fields user cannot view
- **`getFieldValue($fieldName, $userRole = null, $useMasking = true)`** - Get field value with permission check and optional masking
- **`getFilteredAttributes($userRole = null, $useMasking = true)`** - Get all viewable fields as array

**Special Handling:**
- Residents viewing their own data can see most fields (except system fields)
- Automatic role detection from session
- Works seamlessly with data masking (1.3)

### 3. Controller Updates

**ResidentController:**
- **`getDemographics()`** - Returns only fields user has permission to view (null for hidden fields)
- **`summary()`** - Returns only viewable fields in summary response

**Filtering Logic:**
- Fields user cannot view return `null`
- `array_filter()` removes null values before returning JSON
- JavaScript in views automatically handles missing fields

### 4. View Updates

**Edit Resident Profile Form (`edit_resident_profile.blade.php`):**
- Wrapped sensitive fields with `@if($resident->canViewField('field_name'))`
- Fields conditionally rendered based on user permissions:
  - `income_level` - Only admin/secretary
  - `education_level` - Admin/secretary/captain/councilor (not nurse)
  - `occupation` - Admin/secretary/captain/councilor (not nurse)
  - `emergency_contact_*` - Admin/secretary/nurse only

**Demographics Modal:**
- Already handles null values correctly
- Only displays fields present in the response
- No changes needed (works automatically with controller filtering)

## Permission Matrix

### Full Access (All Fields)
- **Admin** - Can view all fields

### Restricted Access by Field Type

#### Basic Information (Most Roles)
- `name`, `email`, `address`, `gender`, `birth_date`, `age`, `marital_status`
- Accessible by: admin, secretary, nurse, captain, councilor, treasurer, resident (own data)

#### Contact Information (Restricted)
- `contact_number`
- Accessible by: admin, secretary, nurse, captain, councilor

#### Demographic Information (Role-Specific)
- `occupation` - admin, secretary, captain, councilor (NOT nurse)
- `family_size` - admin, secretary, captain, councilor, treasurer
- `education_level` - admin, secretary, captain, councilor (NOT nurse)
- `income_level` - admin, secretary ONLY (highly sensitive)
- `employment_status` - admin, secretary, captain, councilor

#### Health Information
- `is_pwd` - admin, secretary, nurse, captain, councilor

#### Emergency Contact (Health-Related)
- `emergency_contact_name` - admin, secretary, nurse
- `emergency_contact_number` - admin, secretary, nurse
- `emergency_contact_relationship` - admin, secretary, nurse

#### System Fields
- `active` - admin, secretary only
- `created_at` - admin, secretary, captain, councilor
- `updated_at` - admin, secretary only

## Usage Examples

### In Blade Templates

```blade
{{-- Conditionally show field --}}
@if($resident->canViewField('income_level'))
    <div>
        <label>Income Level</label>
        <input value="{{ $resident->income_level }}">
    </div>
@endif

{{-- Show field with masking if viewable --}}
@if($resident->canViewField('contact_number'))
    <div>Contact: {{ $resident->getMaskedContactNumber() }}</div>
@endif
```

### In Controllers

```php
// Get only viewable fields
$demographics = [
    'income_level' => $resident->canViewField('income_level') 
        ? $resident->income_level 
        : null,
    // ... other fields
];

// Filter out null values
return response()->json(array_filter($demographics));
```

### In Models

```php
// Check if field is viewable
if ($resident->canViewField('income_level')) {
    $income = $resident->income_level;
}

// Get all viewable fields
$viewableFields = $resident->getViewableFields();

// Get filtered attributes
$filtered = $resident->getFilteredAttributes();
```

## Integration with Other Security Features

### Works with Data Masking (1.3)
- Field-level access control determines if field is visible
- Data masking determines how field is displayed (if visible)
- Example: Nurse can see `contact_number` (field visible) but it's masked (partial)

### Works with Encryption (1.1)
- Encrypted fields are automatically decrypted when accessed
- Field-level access control prevents access before decryption
- Example: Treasurer cannot view `contact_number` (field hidden, never decrypted)

## Security Benefits

1. **Granular Control**: Different roles see different fields
2. **Defense in Depth**: Works with encryption and masking
3. **Privacy Protection**: Sensitive fields completely hidden from unauthorized roles
4. **Compliance**: Helps meet data privacy requirements
5. **Audit Trail**: Can be extended to log field access

## Testing

To test field-level access control:

1. **Login as different roles** and view resident edit form
2. **Check that fields are hidden** based on role:
   - Nurse should NOT see: `income_level`, `occupation`, `education_level`
   - Captain should NOT see: `income_level`, `emergency_contact_*`
   - Treasurer should NOT see: Most sensitive fields
3. **Verify demographics modal** only shows permitted fields
4. **Test API responses** return null for hidden fields

## Files Modified

1. `app/Helpers/FieldPermission.php` - New permission system
2. `app/Models/Residents.php` - Added field visibility methods
3. `app/Http/Controllers/AdminControllers/UserManagementControllers/ResidentController.php` - Updated to filter fields
4. `resources/views/admin/residents/edit_resident_profile.blade.php` - Added conditional field visibility

## Future Enhancements

1. **Permission Configuration**: Move permission matrix to config file for easier management
2. **Audit Logging**: Log when fields are accessed/viewed
3. **Dynamic Permissions**: Allow per-user permission overrides
4. **Permission Groups**: Create permission groups for easier management
5. **Field-Level Edit Permissions**: Separate view and edit permissions

## Notes

- Field-level access control works at the application level
- Hidden fields are not sent to the client (better than just hiding in CSS)
- Works seamlessly with existing route-level access control
- Can be combined with data masking for maximum security
- Permission matrix is easily configurable in `FieldPermission.php`

