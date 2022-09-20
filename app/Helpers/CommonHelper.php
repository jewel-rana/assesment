<?php

namespace App\Helpers;

use App\Constants\CommissionConstant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CommonHelper
{
    public static function getCommissionRate($transactionType, $clientType): float
    {
        return config('commission.fees')[strtolower($transactionType)][$clientType] ?? CommissionConstant::DEFAULT_COMMISSION_RATE;
    }

    public static function formatCommission(float $commissionAmount): float
    {
        return round($commissionAmount, 2);
    }

    public static function formatNumber($amount, $format = 'round', $decimal = 2)
    {
        return self::{$format}($amount, $decimal);
    }

    protected function ceil($number, $decimal = 0)
    {
        return ceil($number);
    }

    protected function round($number, $decimal = 0): float
    {
        return round($number, $decimal);
    }

    protected function floor($number)
    {
        return floor($number);
    }

    protected function float($number, $decimal = 2): float
    {
        return round($number, $decimal, PHP_ROUND_HALF_UP);
    }

    public static function firstDateOfWeek($date): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00')->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    }

    public static function lastDateOfWeek($date): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00')->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
    }
}
