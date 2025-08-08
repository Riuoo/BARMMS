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
        'medical_logbook_id',
        'request_date',
        'quantity_requested',
        'quantity_approved',
        'status',
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

    public function medicalLogbook()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
