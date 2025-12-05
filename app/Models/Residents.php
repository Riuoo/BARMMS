<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use App\Helpers\DataMasking;
use App\Helpers\FieldPermission;

class Residents extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'gender',
        'contact_number',
        'birth_date',
        'marital_status',
        'occupation',
        'age',
        'family_size',
        'education_level',
        'income_level',
        'employment_status',
        'is_pwd',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'active',
        'qr_code_token',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_enabled_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'active' => 'boolean',
            'is_pwd' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_enabled_at' => 'datetime',
            'contact_number' => 'encrypted', // Encrypt contact number
            'emergency_contact_name' => 'encrypted', // Encrypt emergency contact name
            'emergency_contact_number' => 'encrypted', // Encrypt emergency contact number
            'emergency_contact_relationship' => 'encrypted', // Encrypt emergency contact relationship
        ];
    }

    /**
     * Check if 2FA is enabled for this user
     * 
     * @return bool
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && !empty($this->two_factor_secret);
    }

    public function patientRecord()
    {
        return $this->hasOne(MedicalRecord::class, 'resident_id');
    }

    public function medicalLogbooks()
    {
        return $this->hasMany(MedicalRecord::class, 'resident_id');
    }

    public function vaccinationRecords()
    {
        return $this->hasMany(VaccinationRecord::class, 'resident_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'resident_id');
    }

    /**
     * Generate or retrieve QR code token for resident
     */
    public function generateQrCodeToken(): string
    {
        if (!$this->qr_code_token) {
            // Generate a unique token using resident ID and a random string
            $this->qr_code_token = hash('sha256', $this->id . '-' . $this->email . '-' . now()->timestamp . '-' . uniqid());
            $this->save();
        }
        return $this->qr_code_token;
    }

    /**
     * Get QR code data URL (for JavaScript QR code generation)
     */
    public function getQrCodeData(): string
    {
        $token = $this->generateQrCodeToken();
        // Return a URL that will be used to verify the QR code
        return route('qr.verify', ['token' => $token]);
    }

    /**
     * Get masked contact number based on current user's role
     * 
     * @param string|null $userRole Optional role override
     * @return string
     */
    public function getMaskedContactNumber(?string $userRole = null): string
    {
        $role = $userRole ?? Session::get('user_role');
        $maskingLevel = DataMasking::getMaskingLevel($role);

        // Check if user is viewing their own data (for residents)
        $isOwnData = $role === 'resident' && Session::get('user_id') == $this->id;

        if ($maskingLevel === 'none' || $isOwnData) {
            return $this->contact_number ?? 'Not provided';
        }

        if ($maskingLevel === 'partial') {
            // Show last 4 digits for partial access
            return DataMasking::maskPhoneNumber($this->contact_number, 4);
        }

        // Full masking - show only last 2 digits
        return DataMasking::maskPhoneNumber($this->contact_number, 2);
    }

    /**
     * Get masked emergency contact name based on current user's role
     * 
     * @param string|null $userRole Optional role override
     * @return string
     */
    public function getMaskedEmergencyContactName(?string $userRole = null): string
    {
        $role = $userRole ?? Session::get('user_role');
        $maskingLevel = DataMasking::getMaskingLevel($role);

        // Check if user is viewing their own data (for residents)
        $isOwnData = $role === 'resident' && Session::get('user_id') == $this->id;

        if ($maskingLevel === 'none' || $isOwnData) {
            return $this->emergency_contact_name ?? 'Not provided';
        }

        if ($maskingLevel === 'partial') {
            // Show first letter of each name part
            return DataMasking::maskName($this->emergency_contact_name, true);
        }

        // Full masking - show only first letter
        return DataMasking::maskName($this->emergency_contact_name, false);
    }

    /**
     * Get masked emergency contact number based on current user's role
     * 
     * @param string|null $userRole Optional role override
     * @return string
     */
    public function getMaskedEmergencyContactNumber(?string $userRole = null): string
    {
        $role = $userRole ?? Session::get('user_role');
        $maskingLevel = DataMasking::getMaskingLevel($role);

        // Check if user is viewing their own data (for residents)
        $isOwnData = $role === 'resident' && Session::get('user_id') == $this->id;

        if ($maskingLevel === 'none' || $isOwnData) {
            return $this->emergency_contact_number ?? 'Not provided';
        }

        if ($maskingLevel === 'partial') {
            // Show last 4 digits for partial access
            return DataMasking::maskPhoneNumber($this->emergency_contact_number, 4);
        }

        // Full masking - show only last 2 digits
        return DataMasking::maskPhoneNumber($this->emergency_contact_number, 2);
    }

    /**
     * Get masked emergency contact relationship based on current user's role
     * 
     * @param string|null $userRole Optional role override
     * @return string
     */
    public function getMaskedEmergencyContactRelationship(?string $userRole = null): string
    {
        $role = $userRole ?? Session::get('user_role');
        $maskingLevel = DataMasking::getMaskingLevel($role);

        // Check if user is viewing their own data (for residents)
        $isOwnData = $role === 'resident' && Session::get('user_id') == $this->id;

        if ($maskingLevel === 'none' || $isOwnData) {
            return $this->emergency_contact_relationship ?? 'Not provided';
        }

        if ($maskingLevel === 'partial') {
            // Show first letter for partial access
            return DataMasking::maskString($this->emergency_contact_relationship, 1);
        }

        // Full masking - completely mask
        return DataMasking::maskString($this->emergency_contact_relationship, 0);
    }

    /**
     * Check if current user can view a specific field
     * 
     * @param string $fieldName
     * @param string|null $userRole Optional role override
     * @return bool
     */
    public function canViewField(string $fieldName, ?string $userRole = null): bool
    {
        $role = $userRole ?? Session::get('user_role');
        $viewingUserId = Session::get('user_id');
        
        return FieldPermission::canViewFieldForResident(
            $fieldName, 
            $role, 
            $viewingUserId, 
            $this->id
        );
    }

    /**
     * Get all fields that current user can view
     * 
     * @param string|null $userRole Optional role override
     * @return array
     */
    public function getViewableFields(?string $userRole = null): array
    {
        $role = $userRole ?? Session::get('user_role');
        $viewingUserId = Session::get('user_id');
        
        // If resident viewing own data, return special fields
        if ($role === 'resident' && $viewingUserId == $this->id) {
            return FieldPermission::getFieldCategories()['basic'] 
                + FieldPermission::getFieldCategories()['contact']
                + FieldPermission::getFieldCategories()['demographic']
                + FieldPermission::getFieldCategories()['health'];
        }
        
        return FieldPermission::getViewableFields($role);
    }

    /**
     * Get all fields that current user cannot view
     * 
     * @param string|null $userRole Optional role override
     * @return array
     */
    public function getHiddenFields(?string $userRole = null): array
    {
        $role = $userRole ?? Session::get('user_role');
        $viewingUserId = Session::get('user_id');
        
        // If resident viewing own data, return system fields only
        if ($role === 'resident' && $viewingUserId == $this->id) {
            return FieldPermission::getFieldCategories()['system'];
        }
        
        return FieldPermission::getHiddenFields($role);
    }

    /**
     * Get field value if user has permission, otherwise return null or masked value
     * 
     * @param string $fieldName
     * @param string|null $userRole Optional role override
     * @param bool $useMasking If true, return masked value if field is viewable but should be masked
     * @return mixed|null
     */
    public function getFieldValue(string $fieldName, ?string $userRole = null, bool $useMasking = true)
    {
        if (!$this->canViewField($fieldName, $userRole)) {
            return null;
        }

        // If field is viewable, check if we should mask it
        if ($useMasking) {
            // Use masking methods for sensitive fields
            switch ($fieldName) {
                case 'contact_number':
                    return $this->getMaskedContactNumber($userRole);
                case 'emergency_contact_name':
                    return $this->getMaskedEmergencyContactName($userRole);
                case 'emergency_contact_number':
                    return $this->getMaskedEmergencyContactNumber($userRole);
                case 'emergency_contact_relationship':
                    return $this->getMaskedEmergencyContactRelationship($userRole);
            }
        }

        // Return raw value (will be decrypted automatically for encrypted fields)
        return $this->getAttribute($fieldName);
    }

    /**
     * Get filtered attributes based on user permissions
     * 
     * @param string|null $userRole Optional role override
     * @param bool $useMasking If true, apply masking to sensitive fields
     * @return array
     */
    public function getFilteredAttributes(?string $userRole = null, bool $useMasking = true): array
    {
        $role = $userRole ?? Session::get('user_role');
        $viewableFields = $this->getViewableFields($role);
        
        $filtered = [];
        foreach ($viewableFields as $field) {
            $value = $this->getFieldValue($field, $role, $useMasking);
            if ($value !== null) {
                $filtered[$field] = $value;
            }
        }
        
        return $filtered;
    }
}
