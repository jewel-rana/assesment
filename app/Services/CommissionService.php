<?php

namespace App\Services;

use App\Helpers\CommonHelper;

class CommissionService
{
    /**
     * @param $user_id
     * @param $amount
     * @param $transaction_type
     * @param $transaction_date
     * @param $client_type
     * @param string $currency
     * @return float
     */
    public function calculate($user_id, $amount, $transaction_type, $transaction_date, $client_type, string $currency = 'EUR'): float
    {
        $commissionAbleAmount = $this->getCommissionAbleAmount($user_id, $amount, $transaction_type, $transaction_date, $client_type, $currency);
        $commissionRate = CommonHelper::getCommissionRate($transaction_type, $client_type);
        $commission = ($commissionAbleAmount> 0) ? $commissionAbleAmount * ($commissionRate / 100) : 0.0;
        return CommonHelper::formatCommission($commission);
    }

    /**
     * @param int $user_id
     * @param float $amount
     * @param string $transaction_type
     * @param string $transaction_date
     * @param string $client_type
     * @param string $currency
     * @return float
     */
    public function getCommissionAbleAmount(int $user_id, float $amount, string $transaction_type, string $transaction_date, string $client_type, string $currency): float
    {
        if (
            in_array($transaction_type, (array)config('commission.free_of_charges.transaction_types'))
            && in_array($client_type, (array)config('commission.free_of_charges.client_types'))
        ) {
            $totalTransactions = (new TransactionService())->all()
                    ->where('user_id', $user_id)
                    ->where('date', '>=', CommonHelper::firstDateOfWeek($transaction_date))
                    ->where('date', '<=', CommonHelper::lastDateOfWeek($transaction_date))
                    ->map(function ($item, $key) {
                        return [
                            'amount' => (new ExchangeRateCalculationService())->calculate($item['amount'], $item['currency'])
                        ];
                    })->sum('amount');

            if ($totalTransactions < config('commission.free_of_charges.amount')) {
                $amountToBeFreeOfCharge = config('commission.free_of_charges.amount') - $totalTransactions;
                $amount = $amount - (new ExchangeRateCalculationService())->calculate($amountToBeFreeOfCharge, 'EUR', $currency);
            }
        }

        return $amount;
    }
}
