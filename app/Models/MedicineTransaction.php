<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'resident_id',
        'medical_record_id',
        'transaction_type',
        'quantity',
        'transaction_date',
        'prescribed_by',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
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

    public function prescribedByUser()
    {
        return $this->belongsTo(BarangayProfile::class, 'prescribed_by');
    }

    public function scopeType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}
