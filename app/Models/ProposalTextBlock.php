<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalTextBlock extends Model
{
    protected $table = 'proposal_text_blocks';

    protected $fillable = [
        'key',
        'title',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
