<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ChildProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'mother_name',
        'contact_number',
        'purok',
        'registered_by',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function registeredBy()
    {
        return $this->belongsTo(Residents::class, 'registered_by');
    }

    public function vaccinationRecords()
    {
        return $this->hasMany(VaccinationRecord::class, 'child_profile_id');
    }

    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    public function getAgeInMonthsAttribute()
    {
        return $this->birth_date->diffInMonths(now());
    }

    public function getAgeInYearsAttribute()
    {
        return $this->birth_date->diffInYears(now());
    }

    public function getAgeGroupAttribute()
    {
        $months = $this->getAgeInMonthsAttribute();
        
        if ($months < 12) {
            return 'Infant';
        } elseif ($months < 36) {
            return 'Toddler';
        } elseif ($months < 144) { // 12 years
            return 'Child';
        } elseif ($months < 216) { // 18 years
            return 'Adolescent';
        } else {
            return 'Adult';
        }
    }

    public function getFormattedAgeAttribute()
    {
        // More precise age display with integers only: X years Y months
        $now = Carbon::now();
        $birth = Carbon::parse($this->birth_date);
        $years = (int) $birth->diffInYears($now);
        $months = (int) $birth->copy()->addYears($years)->diffInMonths($now);

        if ($years === 0) {
            return $months . ' month' . ($months === 1 ? '' : 's');
        }
        if ($months === 0) {
            return $years . ' year' . ($years === 1 ? '' : 's');
        }
        return $years . ' year' . ($years === 1 ? '' : 's') . ' ' . $months . ' month' . ($months === 1 ? '' : 's');
    }

    public function getNextVaccinationsDueAttribute()
    {
        // This would be implemented based on vaccination schedules
        return collect();
    }

    public function getVaccinationStatusAttribute()
    {
        $records = $this->vaccinationRecords;
        $total = $records->count();
        $completed = $records->where('is_complete', true)->count();
        
        if ($total === 0) {
            return 'No Vaccinations';
        }
        
        return "{$completed} of {$total} completed";
    }
}
