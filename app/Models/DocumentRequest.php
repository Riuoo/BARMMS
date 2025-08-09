<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'description',
        'status',
        'is_read',
        'resident_is_read',
    ];

    public function user()
    {
        return $this->belongsTo(Residents::class);
    }
}
