<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'resident_id',
        'medical_record_id',
        'request_date',
        'quantity_requested',
        'quantity_approved',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function resident()
    {
        return $this->belongsTo(Residents::class);
    }
    

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(BarangayProfile::class, 'approved_by');
    }
}
