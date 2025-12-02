<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        ];
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
}
