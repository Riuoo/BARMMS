<?php

namespace App\Helpers;

class DataMasking
{
    /**
     * Mask a phone number, showing only last 4 digits
     * Example: 09123456789 -> ****-****-6789
     * 
     * @param string|null $phoneNumber
     * @param int $visibleDigits Number of digits to show at the end
     * @return string
     */
    public static function maskPhoneNumber(?string $phoneNumber, int $visibleDigits = 4): string
    {
        if (empty($phoneNumber)) {
            return 'Not provided';
        }

        // Remove any non-digit characters
        $digits = preg_replace('/\D/', '', $phoneNumber);
        
        if (strlen($digits) <= $visibleDigits) {
            return str_repeat('*', strlen($digits));
        }

        $visible = substr($digits, -$visibleDigits);
        $masked = str_repeat('*', strlen($digits) - $visibleDigits);
        
        // Format with dashes for better readability
        if (strlen($digits) >= 11) {
            return substr($masked, 0, 4) . '-' . substr($masked, 4, 4) . '-' . $visible;
        } elseif (strlen($digits) >= 7) {
            return substr($masked, 0, 3) . '-' . substr($masked, 3) . '-' . $visible;
        }
        
        return $masked . '-' . $visible;
    }

    /**
     * Mask a name, showing only first letter and last name
     * Example: "Juan Dela Cruz" -> "J*** D*** C***"
     * 
     * @param string|null $name
     * @param bool $showLastName Show last name or mask it completely
     * @return string
     */
    public static function maskName(?string $name, bool $showLastName = false): string
    {
        if (empty($name)) {
            return 'Not provided';
        }

        $parts = explode(' ', trim($name));
        
        if (count($parts) === 1) {
            // Single name - show first letter only
            return substr($name, 0, 1) . str_repeat('*', max(0, strlen($name) - 1));
        }

        $masked = [];
        foreach ($parts as $index => $part) {
            if ($index === 0) {
                // First name - show first letter
                $masked[] = substr($part, 0, 1) . str_repeat('*', max(0, strlen($part) - 1));
            } elseif ($index === count($parts) - 1 && $showLastName) {
                // Last name - show first letter if showLastName is true
                $masked[] = substr($part, 0, 1) . str_repeat('*', max(0, strlen($part) - 1));
            } else {
                // Middle names - mask completely or show first letter
                $masked[] = substr($part, 0, 1) . str_repeat('*', max(0, strlen($part) - 1));
            }
        }

        return implode(' ', $masked);
    }

    /**
     * Mask a string with asterisks, showing only specified characters
     * 
     * @param string|null $value
     * @param int $visibleChars Number of characters to show at the end
     * @param string $maskChar Character to use for masking
     * @return string
     */
    public static function maskString(?string $value, int $visibleChars = 0, string $maskChar = '*'): string
    {
        if (empty($value)) {
            return 'Not provided';
        }

        if (strlen($value) <= $visibleChars) {
            return str_repeat($maskChar, strlen($value));
        }

        $visible = substr($value, -$visibleChars);
        $masked = str_repeat($maskChar, strlen($value) - $visibleChars);
        
        return $masked . $visible;
    }

    /**
     * Get masking level based on user role
     * 
     * @param string|null $userRole
     * @return string 'full', 'partial', or 'none'
     */
    public static function getMaskingLevel(?string $userRole): string
    {
        if (empty($userRole)) {
            return 'full';
        }

        // Admin and secretary can see full data
        if (in_array($userRole, ['admin', 'secretary'])) {
            return 'none';
        }

        // Nurse can see partial data (for health-related purposes)
        if ($userRole === 'nurse') {
            return 'partial';
        }

        // Captain and councilor see partial data
        if (in_array($userRole, ['captain', 'councilor'])) {
            return 'partial';
        }

        // Treasurer and other roles see masked data
        if ($userRole === 'treasurer') {
            return 'full';
        }

        // Residents see their own data unmasked, but others' data masked
        if ($userRole === 'resident') {
            return 'partial';
        }

        // Default to full masking for unknown roles
        return 'full';
    }

    /**
     * Check if current user can view unmasked sensitive data
     * 
     * @param string|null $userRole
     * @return bool
     */
    public static function canViewUnmasked(?string $userRole): bool
    {
        return self::getMaskingLevel($userRole) === 'none';
    }

    /**
     * Check if current user can view partially masked data
     * 
     * @param string|null $userRole
     * @return bool
     */
    public static function canViewPartial(?string $userRole): bool
    {
        $level = self::getMaskingLevel($userRole);
        return in_array($level, ['none', 'partial']);
    }
}

