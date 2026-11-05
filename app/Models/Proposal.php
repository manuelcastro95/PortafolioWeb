<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'workana_url',
        'project_title',
        'project_description',
        'skills',
        'published_at',
        'proposals_count',
        'client_name',
        'client_published_projects',
        'client_paid_projects',
        'client_member_since',
        'hourly_rate_usd',
        'workana_commission',
        'usd_to_cop_rate',
        'phases',
        'workana_tasks',
        'text_blocks',
        'details_text',
        'delivery_time',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'phases' => 'array',
        'workana_tasks' => 'array',
        'text_blocks' => 'array',
        'published_at' => 'date',
        'sent_at' => 'date',
        'hourly_rate_usd' => 'decimal:2',
        'workana_commission' => 'decimal:2',
        'usd_to_cop_rate' => 'decimal:4',
    ];

    protected $appends = [
        'total_hours',
        'total_cop',
        'effective_hourly_cop',
        'effective_hourly_usd',
        'commission_cop',
        'net_cop',
        'phase_breakdown',
    ];

    /**
     * Tasa COP efectiva usada para los cálculos.
     */
    public function copRate(): float
    {
        return (float) ($this->usd_to_cop_rate ?: 3245);
    }

    /**
     * Horas de una fase = suma de horas de sus ítems.
     */
    protected function phaseHours(array $phase): float
    {
        $items = $phase['items'] ?? [];
        return collect($items)->sum(fn ($item) => (float) ($item['hours'] ?? 0));
    }

    /**
     * COP de una fase: monto manual si existe, si no horas * tarifa * tasa.
     */
    protected function phaseCop(array $phase): float
    {
        if (isset($phase['amount_cop']) && $phase['amount_cop'] !== null && $phase['amount_cop'] !== '') {
            return (float) $phase['amount_cop'];
        }

        $hours = $this->phaseHours($phase);
        return round($hours * (float) $this->hourly_rate_usd * $this->copRate());
    }

    public function getPhaseBreakdownAttribute(): array
    {
        return collect($this->phases ?? [])->map(function ($phase) {
            return [
                'name' => $phase['name'] ?? '',
                'items' => $phase['items'] ?? [],
                'amount_cop' => $phase['amount_cop'] ?? null,
                'hours' => $this->phaseHours($phase),
                'cop' => $this->phaseCop($phase),
            ];
        })->values()->all();
    }

    public function getTotalHoursAttribute(): float
    {
        return collect($this->phases ?? [])->sum(fn ($phase) => $this->phaseHours($phase));
    }

    public function getTotalCopAttribute(): float
    {
        return collect($this->phases ?? [])->sum(fn ($phase) => $this->phaseCop($phase));
    }

    public function getEffectiveHourlyCopAttribute(): float
    {
        $hours = $this->total_hours;
        return $hours > 0 ? round($this->total_cop / $hours) : 0;
    }

    public function getEffectiveHourlyUsdAttribute(): float
    {
        $rate = $this->copRate();
        return $rate > 0 ? round($this->effective_hourly_cop / $rate, 2) : 0;
    }

    public function getCommissionCopAttribute(): float
    {
        return round($this->total_cop * ((float) $this->workana_commission / 100));
    }

    public function getNetCopAttribute(): float
    {
        return $this->total_cop - $this->commission_cop;
    }
}
