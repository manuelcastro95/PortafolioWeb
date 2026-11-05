<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalTaskCatalog extends Model
{
    protected $table = 'proposal_task_catalog';

    protected $fillable = [
        'name',
        'default_hours',
        'category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'default_hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
