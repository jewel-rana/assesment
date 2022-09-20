<?php

namespace App\Helpers;

use App\Constants\CommissionConstant;

class CommonHelper
{
    public static function getCommissionRate($transactionType, $clientType): float
    {
        return config('commission.rages')[strtolower($transactionType)][$clientType] ?? CommissionConstant::DEFAULT_COMMISSION_RATE;
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
}
