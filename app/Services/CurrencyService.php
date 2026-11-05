<?php

namespace App\Services;

use App\Models\ProposalSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    private const CACHE_KEY = 'usd_cop_rate';
    private const ENDPOINT = 'https://open.er-api.com/v6/latest/USD';

    /**
     * Tasa USD -> COP. Cacheada 24 h. Si la API falla se usa el valor de
     * respaldo configurable (hoy ~3.245).
     */
    public function usdToCop(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addHours(24), function () {
            try {
                $response = Http::timeout(8)->get(self::ENDPOINT);

                if ($response->ok()) {
                    $data = $response->json();
                    $rate = data_get($data, 'rates.COP');

                    if (is_numeric($rate) && $rate > 0) {
                        return [
                            'rate' => (float) $rate,
                            'source' => 'api',
                            'fetched_at' => now()->toDateTimeString(),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CurrencyService: fallo al obtener tasa USD->COP', [
                    'error' => $e->getMessage(),
                ]);
            }

            return [
                'rate' => (float) ProposalSetting::current()->fallback_cop_rate,
                'source' => 'fallback',
                'fetched_at' => now()->toDateTimeString(),
            ];
        });
    }

    public function rate(bool $forceRefresh = false): float
    {
        return $this->usdToCop($forceRefresh)['rate'];
    }
}
