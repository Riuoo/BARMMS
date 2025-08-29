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
        'health_status',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'active',
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
}
