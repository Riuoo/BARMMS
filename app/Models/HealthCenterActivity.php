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
        'image',
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
        'audience_scope',
        'audience_purok',
        'reminder_sent',
        'is_featured',
    ];

    protected $casts = [
        'activity_date' => 'date',
        // Store times as strings (HH:MM:SS); casting to datetime can break when date is absent
        'start_time' => 'string',
        'end_time' => 'string',
        'budget' => 'decimal:2',
        'reminder_sent' => 'boolean',
        'is_featured' => 'boolean',
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
            try {
                $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time);
                $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time);
                return $start->diffInMinutes($end);
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'event_id')
            ->where('event_type', 'health_center_activity');
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

    /**
     * Get a human-readable audience label.
     */
    public function getAudienceLabelAttribute(): string
    {
        if ($this->audience_scope === 'purok' && $this->audience_purok) {
            return 'Purok ' . $this->audience_purok;
        }

        return 'All Residents';
    }
} 