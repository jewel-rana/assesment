<?php

namespace App\Services;

use App\Constants\ExchangeRateConstant;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateCalculationService
{
    protected array $rates;

    public function __construct()
    {
        $this->initRates();
    }

    /**
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     */
    public function calculate(float $amount, string $from = 'USD', string $to = 'EUR'): float
    {
        if(array_key_exists($from, $this->rates) && array_key_exists($to, $this->rates)) {
            $amount = $amount / ($this->rates[$from] / $this->rates[$to]);
        }

        return CommonHelper::formatNumber($amount);
    }

    /**
     * This method fetch currency exchange rates from API, and save to cache for two hours
     * So that in next query it will not call api instead cache
     * @return void
     */
    private function initRates()
    {
        if(Cache::has('exchange_rates')) {
            $this->rates = Cache::get('exchange_rates');
        } else {
            Log::info('Exchange rate api called');
            $response = Http::get('https://developers.paysera.com/tasks/api/currency-exchange-rates');
            if ($response->successful()) {
                if(array_key_exists('rates', $response->json())) {
                    Log::info('Exchange rates', $response->json());
                    $this->rates = $response->json()['rates'];
                    Cache::remember('exchange_rates', 120, function() use ($response) {
                        return $response->json()['rates'];
                    });
                }
            }
        }
    }
}
