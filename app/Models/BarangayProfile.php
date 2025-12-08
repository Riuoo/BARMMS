<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BarangayProfile extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'password',
        'role',
        'address',
        'contact_number',
        'active',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_enabled_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_enabled_at' => 'datetime',
        ];
    }

    /**
     * Check if 2FA is enabled for this user
     * 
     * @return bool
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && !empty($this->two_factor_secret);
    }

    /**
     * Get full name by combining first_name, middle_name, last_name, and suffix
     * 
     * @return string
     */
    public function getFullNameAttribute(): string
    {
            $parts = array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
                $this->suffix
            ], function($part) {
                return !empty(trim($part ?? ''));
            });
            
        return implode(' ', $parts) ?: 'N/A';
        }
        
    /**
     * Scope to search by full name (using CONCAT)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFullName($query, $search)
    {
        return $query->whereRaw(
            "CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?",
            ["%{$search}%"]
        );
    }

    /**
     * Scope to find exact full name match
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $fullName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFullNameExact($query, $fullName)
    {
        return $query->whereRaw(
            "TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))) = ?",
            [trim($fullName)]
        );
    }
}
