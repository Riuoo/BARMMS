<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'attending_health_worker_id',
        'consultation_datetime',
        'consultation_type',
        'chief_complaint',
        'symptoms',
        'diagnosis',
        'prescribed_medications',
        'temperature',
        'blood_pressure',
        'pulse_rate',
        'respiratory_rate',
        'notes',
        'follow_up_date',
        'follow_up_notes',
    ];

    protected $casts = [
        'consultation_datetime' => 'datetime',
        'follow_up_date' => 'date',
        'temperature' => 'decimal:1',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id')
            ->select(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'active']);
    }

    public function attendingHealthWorker()
    {
        return $this->belongsTo(BarangayProfile::class, 'attending_health_worker_id')
            ->select(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'active']);
    }

    public function medicineRequests()
    {
        return $this->hasMany(MedicineRequest::class);
    }
} 