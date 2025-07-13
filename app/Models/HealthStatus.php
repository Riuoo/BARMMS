<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'concern_type',
        'severity',
        'description',
        'contact_number',
        'emergency_contact',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Residents::class, 'user_id');
    }
}
