<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_identifier',
        'device_fingerprint',
        'user_agent',
        'expires_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this trusted device
     */
    public function user()
    {
        return $this->belongsTo(BarangayProfile::class, 'user_id');
    }
}
