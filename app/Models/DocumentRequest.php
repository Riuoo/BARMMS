<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'document_type',
        'description',
        'status',
        'is_read',
        'resident_is_read',
        'document_template_id',
        'additional_data',
    ];

    protected $casts = [
        'additional_data' => 'array',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function documentTemplate()
    {
        return $this->belongsTo(DocumentTemplate::class);
    }
}
