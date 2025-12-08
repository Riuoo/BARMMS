# Name Field Migration Notes

## Overview
Both `residents` and `barangay_profiles` tables now have separate name fields (`first_name`, `middle_name`, `last_name`, `suffix`) in addition to the combined `name` field.

## Current Status

### Residents Table
- ✅ Has separate fields: `first_name`, `middle_name`, `last_name`, `suffix`
- ✅ `name` field is **kept for backward compatibility** but is **auto-generated** from separate fields
- ✅ The `name` field is redundant but maintained to avoid breaking existing code

### Barangay Profiles Table
- ✅ Has separate fields: `first_name`, `middle_name`, `last_name`, `suffix` (added via migration)
- ✅ `name` field is **kept for backward compatibility** but is **auto-generated** from separate fields
- ✅ The `name` field is redundant but maintained to avoid breaking existing code

## Why Keep the `name` Field?

The `name` field is still used in many places:
1. **Database queries**: WHERE clauses, LIKE searches, ORDER BY
2. **Display in views**: Many views still reference `$resident->name` or `$barangayProfile->name`
3. **Duplicate checking**: Used in account request validation
4. **Search functionality**: Used in search queries across the application
5. **Backward compatibility**: Old records may only have the `name` field populated

## Auto-Generation

Both models (`Residents` and `BarangayProfile`) have a `boot()` method that automatically generates the `name` field from separate parts when saving:

```php
static::saving(function ($model) {
    if ($model->first_name || $model->last_name) {
        $parts = array_filter([
            $model->first_name,
            $model->middle_name,
            $model->last_name,
            $model->suffix
        ], function($part) {
            return !empty(trim($part ?? ''));
        });
        $model->name = implode(' ', $parts);
    }
});
```

## Full Name Accessor

Both models have a `getFullNameAttribute()` accessor that:
1. Combines separate fields if they exist
2. Falls back to the `name` field for old records

Usage: `$resident->full_name` or `$barangayProfile->full_name`

## Migration Strategy

### Phase 1: Current (Completed)
- ✅ Add separate fields to both tables
- ✅ Auto-generate `name` field from parts
- ✅ Update forms to collect separate fields
- ✅ Update controllers to store separate fields
- ✅ Update search queries to use CONCAT for separate fields

### Phase 2: Future (Optional)
- Update all views to use `full_name` accessor instead of `name`
- Update all queries to use CONCAT or full_name accessor
- Consider removing `name` field in a future major version (requires comprehensive refactoring)

## Best Practices

1. **New Code**: Always use separate fields (`first_name`, `middle_name`, `last_name`, `suffix`) when creating/updating records
2. **Display**: Use `$model->full_name` accessor for display (it handles both new and old records)
3. **Search**: Use CONCAT queries to search across separate fields (already implemented in controllers)
4. **Avoid**: Directly setting the `name` field (it's auto-generated)

## Database Schema

### Residents Table
```sql
name VARCHAR(255) -- Redundant, auto-generated from parts
first_name VARCHAR(255) -- Primary field
middle_name VARCHAR(255) -- Optional
last_name VARCHAR(255) -- Primary field
suffix VARCHAR(255) -- Optional
```

### Barangay Profiles Table
```sql
name VARCHAR(255) -- Redundant, auto-generated from parts
first_name VARCHAR(255) -- Primary field
middle_name VARCHAR(255) -- Optional
last_name VARCHAR(255) -- Primary field
suffix VARCHAR(255) -- Optional
```

## Notes

- The `name` field is **redundant** but **kept for backward compatibility**
- All new records should populate separate fields
- The `name` field will be automatically maintained
- Removing the `name` field would require extensive refactoring and is not recommended at this time
