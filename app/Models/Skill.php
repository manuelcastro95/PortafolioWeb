<?php

namespace App\Models;

use App\Support\StorageDisk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function getIconUrlAttribute()
    {
        if (!$this->icon) {
            return null;
        }
        $diskName = StorageDisk::name();
        return Storage::disk($diskName)->url($this->icon);
    }
}

