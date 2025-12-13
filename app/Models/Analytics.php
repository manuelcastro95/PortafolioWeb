<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $table = 'analytics';

    protected $fillable = [
        'event_type',
        'event_name',
        'section',
        'target_url',
        'ip_address',
        'user_agent',
        'referrer',
        'device_type',
        'browser',
        'os',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Scope para filtrar por tipo de evento
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope para filtrar por nombre de evento
     */
    public function scopeOfName($query, $name)
    {
        return $query->where('event_name', $name);
    }

    /**
     * Scope para filtrar por sección
     */
    public function scopeOfSection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope para un rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

