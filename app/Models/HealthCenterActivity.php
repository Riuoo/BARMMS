<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCenterActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_name',
        'activity_type',
        'activity_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'objectives',
        'target_participants',
        'actual_participants',
        'organizer',
        'materials_needed',
        'budget',
        'outcomes',
        'challenges',
        'recommendations',
        'status',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'budget' => 'decimal:2',
    ];

    public function getParticipationRateAttribute()
    {
        if ($this->target_participants && $this->actual_participants) {
            return round(($this->actual_participants / $this->target_participants) * 100, 1);
        }
        return null;
    }

    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInHours($this->end_time);
        }
        return null;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('activity_date', '>=', now()->toDateString())
                    ->where('status', 'Planned');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }
} 