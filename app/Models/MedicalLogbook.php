<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalLogbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'consultation_date',
        'consultation_time',
        'consultation_type',
        'chief_complaint',
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'prescribed_medications',
        'lab_tests_ordered',
        'lab_results',
        'temperature',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'pulse_rate',
        'weight_kg',
        'height_cm',
        'physical_examination',
        'notes',
        'attending_health_worker',
        'follow_up_date',
        'status',
    ];

    protected $casts = [
        'consultation_date' => 'date',
        'consultation_time' => 'datetime',
        'follow_up_date' => 'date',
        'temperature' => 'decimal:1',
        'weight_kg' => 'decimal:2',
        'height_cm' => 'decimal:2',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function getBloodPressureAttribute()
    {
        if ($this->blood_pressure_systolic && $this->blood_pressure_diastolic) {
            return $this->blood_pressure_systolic . '/' . $this->blood_pressure_diastolic;
        }
        return null;
    }

    public function getBloodPressureCategoryAttribute()
    {
        if (!$this->blood_pressure_systolic || !$this->blood_pressure_diastolic) {
            return null;
        }

        $systolic = $this->blood_pressure_systolic;
        $diastolic = $this->blood_pressure_diastolic;

        if ($systolic < 120 && $diastolic < 80) return 'Normal';
        if ($systolic < 130 && $diastolic < 80) return 'Elevated';
        if ($systolic < 140 && $diastolic < 90) return 'Stage 1 Hypertension';
        return 'Stage 2 Hypertension';
    }
} 