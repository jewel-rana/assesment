<?php

namespace App\Services;

use App\Helpers\CommonHelper;

class CommissionService
{
    public function calculate($amount, $transactionType, $clientType, $currency = 'EUR'): float
    {
        $commissionRate = CommonHelper::getCommissionRate($transactionType, $clientType);
        $commission = $amount * ($commissionRate / 100);
        return CommonHelper::formatCommission($commission);
    }
}
