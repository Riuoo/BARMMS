<?php

namespace App\Helpers;

class FieldPermission
{
    /**
     * Permission matrix defining which roles can view which fields
     * 
     * Structure: 'field_name' => ['allowed_roles']
     * 
     * Roles: secretary, nurse, captain, councilor, treasurer, resident
     */
    private static array $permissionMatrix = [
        // Basic Information (most roles can see)
        'name' => ['secretary', 'nurse', 'captain', 'councilor', 'treasurer', 'resident'],
        'email' => ['secretary', 'nurse', 'captain', 'councilor'],
        'address' => ['secretary', 'nurse', 'captain', 'councilor', 'treasurer', 'resident'],
        'gender' => ['secretary', 'nurse', 'captain', 'councilor'],
        'birth_date' => ['secretary', 'nurse', 'captain', 'councilor'],
        'age' => ['secretary', 'nurse', 'captain', 'councilor', 'treasurer', 'resident'],
        'marital_status' => ['secretary', 'nurse', 'captain', 'councilor'],
        
        // Contact Information (restricted)
        'contact_number' => ['secretary', 'nurse', 'captain', 'councilor'],
        
        // Demographic Information
        'occupation' => ['secretary', 'captain', 'councilor'],
        'family_size' => ['secretary', 'captain', 'councilor', 'treasurer'],
        'education_level' => ['secretary', 'captain', 'councilor'],
        'income_level' => ['secretary'], // Highly sensitive - only secretary
        'employment_status' => ['secretary', 'captain', 'councilor'],
        
        // Health Information (nurse-specific)
        'is_pwd' => ['secretary', 'nurse', 'captain', 'councilor'],
        
        // Emergency Contact Information (restricted)
        'emergency_contact_name' => ['secretary', 'nurse'],
        'emergency_contact_number' => ['secretary', 'nurse'],
        'emergency_contact_relationship' => ['secretary', 'nurse'],
        
        // System Fields
        'active' => ['secretary'],
        'created_at' => ['secretary', 'captain', 'councilor'],
        'updated_at' => ['secretary'],
    ];

    /**
     * Check if a role can view a specific field
     * 
     * @param string $fieldName
     * @param string|null $userRole
     * @return bool
     */
    public static function canViewField(string $fieldName, ?string $userRole = null): bool
    {
        if (empty($userRole)) {
            return false;
        }

        // Check permission matrix
        $allowedRoles = self::$permissionMatrix[$fieldName] ?? [];
        
        return in_array($userRole, $allowedRoles);
    }

    /**
     * Get all fields that a role can view
     * 
     * @param string|null $userRole
     * @return array
     */
    public static function getViewableFields(?string $userRole = null): array
    {
        if (empty($userRole)) {
            return [];
        }

        $viewableFields = [];
        foreach (self::$permissionMatrix as $fieldName => $allowedRoles) {
            if (in_array($userRole, $allowedRoles)) {
                $viewableFields[] = $fieldName;
            }
        }

        return $viewableFields;
    }

    /**
     * Get all fields that a role cannot view
     * 
     * @param string|null $userRole
     * @return array
     */
    public static function getHiddenFields(?string $userRole = null): array
    {
        if (empty($userRole)) {
            return array_keys(self::$permissionMatrix);
        }

        $hiddenFields = [];
        foreach (self::$permissionMatrix as $fieldName => $allowedRoles) {
            if (!in_array($userRole, $allowedRoles)) {
                $hiddenFields[] = $fieldName;
            }
        }

        return $hiddenFields;
    }

    /**
     * Filter an array of data to only include viewable fields for a role
     * 
     * @param array $data
     * @param string|null $userRole
     * @return array
     */
    public static function filterFields(array $data, ?string $userRole = null): array
    {
        if (empty($userRole)) {
            return [];
        }

        $viewableFields = self::getViewableFields($userRole);
        
        return array_intersect_key($data, array_flip($viewableFields));
    }

    /**
     * Check if resident can view their own field (special case for residents)
     * 
     * @param string $fieldName
     * @param string|null $userRole
     * @param int|null $viewingUserId
     * @param int|null $residentId
     * @return bool
     */
    public static function canViewFieldForResident(
        string $fieldName, 
        ?string $userRole = null, 
        ?int $viewingUserId = null, 
        ?int $residentId = null
    ): bool {
        // If resident is viewing their own data, they can see most fields
        if ($userRole === 'resident' && $viewingUserId && $residentId && $viewingUserId == $residentId) {
            // Residents can view their own basic info, but not system fields
            $ownDataFields = [
                'name', 'email', 'address', 'gender', 'birth_date', 'age', 
                'marital_status', 'contact_number', 'occupation', 'family_size',
                'education_level', 'employment_status', 'is_pwd',
                'emergency_contact_name', 'emergency_contact_number', 
                'emergency_contact_relationship'
            ];
            
            return in_array($fieldName, $ownDataFields);
        }

        // Use standard permission check
        return self::canViewField($fieldName, $userRole);
    }

    /**
     * Get permission matrix (for documentation/debugging)
     * 
     * @return array
     */
    public static function getPermissionMatrix(): array
    {
        return self::$permissionMatrix;
    }

    /**
     * Get field categories for better organization
     * 
     * @return array
     */
    public static function getFieldCategories(): array
    {
        return [
            'basic' => ['name', 'email', 'address', 'gender', 'birth_date', 'age', 'marital_status'],
            'contact' => ['contact_number', 'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relationship'],
            'demographic' => ['occupation', 'family_size', 'education_level', 'income_level', 'employment_status'],
            'health' => ['is_pwd'],
            'system' => ['active', 'created_at', 'updated_at'],
        ];
    }
}

