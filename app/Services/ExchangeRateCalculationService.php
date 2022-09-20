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

    public function calculate($amount, $from = 'USD', $to = 'EUR')
    {
        if(array_key_exists($from, $this->rates) && array_key_exists($to, $this->rates)) {
            $amount = $amount / ($this->rates[$from] / $this->rates[$to]);
        }

        return CommonHelper::formatNumber($amount);
    }

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
