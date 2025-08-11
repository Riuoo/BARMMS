<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'title',
        'category',
        'description',
        'location',
        'status',
        'media',
        'is_read',
        'assigned_at',
        'resolved_at',
    ];

    protected $casts = [
        'media' => 'array',
        'is_read' => 'boolean',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    /**
     * Get the media files as an array
     */
    public function getMediaFilesAttribute()
    {
        return $this->media ?? [];
    }

    /**
     * Check if the complaint has media files
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

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'gray',
            'under_review' => 'blue',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'purple',
            default => 'gray'
        };
    }

    /**
     * Scope for unread complaints
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
} 