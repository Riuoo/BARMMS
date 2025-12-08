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
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'full_name',
        'address',
        'status',
        'rejection_reason',
        'token',
        'is_read',
        'verification_documents',
    ];

    protected $casts = [
        'verification_documents' => 'array',
    ];
}
