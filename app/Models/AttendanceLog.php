<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccomplishedProject;
use App\Models\HealthCenterActivity;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'guest_name',
        'guest_contact',
        'event_id',
        'event_type',
        'scanned_by',
        'scanned_at',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function event()
    {
        // For event_type === 'event', this now points to AccomplishedProject
        return $this->belongsTo(AccomplishedProject::class, 'event_id');
    }

    public function healthCenterActivity()
    {
        return $this->belongsTo(HealthCenterActivity::class, 'event_id');
    }

    /**
     * Get the event name (either Event or HealthCenterActivity) based on event_type
     */
    public function getEventNameAttribute()
    {
        if ($this->event_type === 'health_center_activity') {
            if ($this->relationLoaded('healthCenterActivity') && $this->healthCenterActivity) {
                return $this->healthCenterActivity->activity_name;
            }
            // If not loaded, try to load it
            $activity = HealthCenterActivity::find($this->event_id);
            return $activity ? $activity->activity_name : 'N/A';
        } elseif ($this->event_type === 'event') {
            if ($this->relationLoaded('event') && $this->event) {
                return $this->event->title;
            }
            // If not loaded, try to load it
            $activity = AccomplishedProject::find($this->event_id);
            return $activity ? $activity->title : 'N/A';
        }
        return 'N/A';
    }

    public function scanner()
    {
        return $this->belongsTo(BarangayProfile::class, 'scanned_by');
    }
}
