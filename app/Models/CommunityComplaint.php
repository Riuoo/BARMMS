<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'description',
        'location',
        'priority',
        'status',
        'media',
        'is_read',
        'admin_notes',
        'resolution_notes',
        'assigned_at',
        'resolved_at',
    ];

    protected $casts = [
        'media' => 'array',
        'is_read' => 'boolean',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Residents::class);
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
     * Get priority color for UI
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
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

    /**
     * Scope for urgent complaints
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }
} 