<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlotterRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'complainant_name',
        'respondent_id',
        'type',
        'description',
        'status',
        'media',
        'is_read',
        'resident_is_read',
    ];

    protected $casts = [
        'media' => 'array',
        'approved_at' => 'datetime',
        'summon_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function respondent()
    {
        return $this->belongsTo(Residents::class, 'respondent_id')
            ->select(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'active']);
    }

    /**
     * Alias for backward compatibility
     */
    public function resident()
    {
        return $this->respondent();
    }

    /**
     * Get the media files as an array
     */
    public function getMediaFilesAttribute()
    {
        return $this->media ?? [];
    }

    /**
     * Check if the blotter has media files
     */
    public function hasMedia()
    {
        return !empty($this->media);
    }

    /**
     * Get the count of media files
     */
    public function getMediaCountAttribute()
    {
        return count($this->media ?? []);
    }
}
