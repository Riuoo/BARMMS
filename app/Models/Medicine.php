<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'category',
        'description',
        'dosage_form',
        'manufacturer',
        'current_stock',
        'minimum_stock',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(MedicineTransaction::class);
    }

    public function requests()
    {
        return $this->hasMany(MedicineRequest::class);
    }

    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= $this->minimum_stock) {
            return 'low';
        }

        return 'sufficient';
    }

    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        
        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry <= 30) {
            return 'expiring_soon';
        } else {
            return 'valid';
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('current_stock', '<=', DB::raw('minimum_stock'));
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('expiry_date', '<=', now()->addDays(30));
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }
}