<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'child_profile_id',
        'vaccine_name',
        'vaccine_type',
        'vaccination_date',
        'dose_number',
        'next_dose_date',
        'administered_by',
        
    ];

    protected $casts = [
        'vaccination_date' => 'date',
        'next_dose_date' => 'date',
        
    ];

    public function administeredByProfile()
    {
        return $this->belongsTo(BarangayProfile::class, 'administered_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($vaccinationRecord) {
            // Ensure either resident_id or child_profile_id is set, but not both
            if (empty($vaccinationRecord->resident_id) && empty($vaccinationRecord->child_profile_id)) {
                throw new \InvalidArgumentException('Either resident_id or child_profile_id must be set.');
            }
            
            if (!empty($vaccinationRecord->resident_id) && !empty($vaccinationRecord->child_profile_id)) {
                throw new \InvalidArgumentException('Only one of resident_id or child_profile_id can be set, not both.');
            }
        });
    }

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function childProfile()
    {
        return $this->belongsTo(ChildProfile::class, 'child_profile_id');
    }

    public function getPatientNameAttribute()
    {
        if ($this->resident) {
            return $this->resident->name;
        }
        if ($this->childProfile) {
            return $this->childProfile->first_name . ' ' . $this->childProfile->last_name;
        }
        return 'Unknown Patient';
    }

    public function getPatientTypeAttribute()
    {
        if ($this->resident) {
            return 'Resident';
        }
        if ($this->childProfile) {
            return 'Child';
        }
        return 'Unknown';
    }

    public function getNextDoseDueAttribute()
    {
        if ($this->next_dose_date) {
            return $this->next_dose_date->isPast();
        }
        return false;
    }

    // Dose progress helpers removed due to simplified schema
} 