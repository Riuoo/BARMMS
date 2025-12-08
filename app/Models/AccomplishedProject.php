<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccomplishedProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'category',
        'location',
        'budget',
        'start_date',
        'completion_date',
        'status',
        'image',
        'beneficiaries',
        'impact',
        'funding_source',
        'implementing_agency',
        'is_featured',
    ];

    protected $casts = [
        'start_date' => 'date',
        'completion_date' => 'date',
        'budget' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function getFormattedBudgetAttribute()
    {
        return $this->budget ? '₱' . number_format($this->budget, 2) : 'N/A';
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->completion_date);
    }

    public function getCategoryColorAttribute()
    {
        $colors = [
            'Infrastructure' => 'bg-blue-100 text-blue-800',
            'Health' => 'bg-green-100 text-green-800',
            'Education' => 'bg-purple-100 text-purple-800',
            'Agriculture' => 'bg-yellow-100 text-yellow-800',
            'Social Services' => 'bg-pink-100 text-pink-800',
            'Environment' => 'bg-emerald-100 text-emerald-800',
            'Livelihood' => 'bg-orange-100 text-orange-800',
        ];

        return $colors[$this->category] ?? 'bg-gray-100 text-gray-800';
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : null;
    }
} 