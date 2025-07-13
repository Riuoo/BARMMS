<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'vaccine_name',
        'vaccine_type',
        'vaccination_date',
        'batch_number',
        'manufacturer',
        'dose_number',
        'next_dose_date',
        'administered_by',
        'side_effects',
        'notes',
    ];

    protected $casts = [
        'vaccination_date' => 'date',
        'next_dose_date' => 'date',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function getNextDoseDueAttribute()
    {
        if ($this->next_dose_date) {
            return $this->next_dose_date->isPast();
        }
        return false;
    }
} 