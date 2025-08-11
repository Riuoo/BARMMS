<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_profile_id',
        'email',
        'status',
        'token',
        'is_read',
    ];
}
