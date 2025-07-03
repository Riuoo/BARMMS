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
    ];

    public function user()
    {
        return $this->belongsTo(Residents::class);
    }
}
