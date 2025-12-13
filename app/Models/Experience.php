<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'company',
        'duration',
        'location',
        'responsibilities',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'responsibilities' => 'array',
    ];
}

