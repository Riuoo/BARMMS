<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'event_type',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'status',
        'qr_attendance_enabled',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'qr_attendance_enabled' => 'boolean',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'event_id');
    }

    public function creator()
    {
        return $this->belongsTo(BarangayProfile::class, 'created_by');
    }

    public function getAttendanceCountAttribute()
    {
        return $this->attendanceLogs()->count();
    }
}
