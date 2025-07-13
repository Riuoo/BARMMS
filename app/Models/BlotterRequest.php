<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlotterRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'type',
        'description',
        'status',
        'media',
        'is_read',
    ];

    protected $casts = [
        'media' => 'array',
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
