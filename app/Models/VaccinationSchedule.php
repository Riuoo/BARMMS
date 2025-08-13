<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vaccine_name',
        'vaccine_type',
        'age_group',
        'age_min_months',
        'age_max_months',
        'age_min_years',
        'age_max_years',
        'dose_number',
        'total_doses_required',
        'interval_months',
        'interval_years',
        'is_booster',
        'is_annual',
        'description',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_booster' => 'boolean',
        'is_annual' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByAgeGroup($query, $ageGroup)
    {
        return $query->where('age_group', $ageGroup);
    }

    public function scopeByVaccineType($query, $vaccineType)
    {
        return $query->where('vaccine_type', $vaccineType);
    }

    public function getAgeRangeAttribute()
    {
        if ($this->age_min_months && $this->age_max_months) {
            return "{$this->age_min_months}-{$this->age_max_months} months";
        }
        
        if ($this->age_min_years && $this->age_max_years) {
            return "{$this->age_min_years}-{$this->age_max_years} years";
        }
        
        return "Age not specified";
    }

    public function getIntervalAttribute()
    {
        if ($this->interval_months) {
            return "{$this->interval_months} month" . ($this->interval_months != 1 ? 's' : '');
        }
        
        if ($this->interval_years) {
            return "{$this->interval_years} year" . ($this->interval_years != 1 ? 's' : '');
        }
        
        return "No interval specified";
    }

    public function isAgeAppropriate($ageInMonths, $ageInYears = null)
    {
        if ($this->age_min_months && $this->age_max_months) {
            return $ageInMonths >= $this->age_min_months && $ageInMonths <= $this->age_max_months;
        }
        
        if ($this->age_min_years && $this->age_max_years && $ageInYears) {
            return $ageInYears >= $this->age_min_years && $ageInYears <= $this->age_max_years;
        }
        
        return false;
    }

    public function getNextDoseDate($lastDoseDate)
    {
        if (!$lastDoseDate) {
            return null;
        }
        
        if ($this->interval_months) {
            return $lastDoseDate->addMonths($this->interval_months);
        }
        
        if ($this->interval_years) {
            return $lastDoseDate->addYears($this->interval_years);
        }
        
        return null;
    }
}
