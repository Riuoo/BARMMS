<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'patient_number',
        'blood_type',
        'allergies',
        'medical_history',
        'family_medical_history',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'blood_pressure_status',
        'height_cm',
        'weight_kg',
        'bmi',
        'current_medications',
        'lifestyle_factors',
        'risk_level',
        'notes',
    ];

    protected $casts = [
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'bmi' => 'decimal:2',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function medicalLogbooks()
    {
        return $this->hasMany(MedicalLogbook::class, 'resident_id', 'resident_id');
    }

    public function vaccinationRecords()
    {
        return $this->hasMany(VaccinationRecord::class, 'resident_id', 'resident_id');
    }

    public function calculateBMI()
    {
        if ($this->height_cm && $this->weight_kg) {
            $height_m = $this->height_cm / 100;
            $this->bmi = round($this->weight_kg / ($height_m * $height_m), 2);
            $this->save();
        }
    }

    public function getBMICategory()
    {
        if (!$this->bmi) return null;
        
        if ($this->bmi < 18.5) return 'Underweight';
        if ($this->bmi < 25) return 'Normal';
        if ($this->bmi < 30) return 'Overweight';
        return 'Obese';
    }
} 