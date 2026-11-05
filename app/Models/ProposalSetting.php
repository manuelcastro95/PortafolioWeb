<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalSetting extends Model
{
    protected $fillable = [
        'default_hourly_rate_usd',
        'default_workana_commission',
        'fallback_cop_rate',
    ];

    protected $casts = [
        'default_hourly_rate_usd' => 'decimal:2',
        'default_workana_commission' => 'decimal:2',
        'fallback_cop_rate' => 'decimal:4',
    ];

    /**
     * Devuelve la fila única de configuración, creándola con valores por
     * defecto si todavía no existe.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([
            'default_hourly_rate_usd' => 18,
            'default_workana_commission' => 10,
            'fallback_cop_rate' => 3245,
        ]);
    }
}
