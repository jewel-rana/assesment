<?php

namespace App\Services;

use App\Constants\ExchangeRateConstant;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateCalculationService
{
    protected array $rates;

    public function __construct()
    {
        $this->initRates();
    }

    public function calculate($amount, $currency = 'USD')
    {
        if($currency !== ExchangeRateConstant::BASE_CURRENCY) {
            $amount = array_key_exists($currency, $this->rates) ? $amount / $this->rates[$currency] : $amount;
        }

        return CommonHelper::formatNumber($amount);
    }

    private function initRates()
    {
        if(Cache::has('exchange_rates')) {
            $this->rates = Cache::get('exchange_rates');
        } else {
            $response = Http::get('https://developers.paysera.com/tasks/api/currency-exchange-rates');
            if ($response->successful()) {
                $this->rates = $response->json()['rates'];
            }
        }
    }
}
