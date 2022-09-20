<?php

namespace App\Services;

use App\Helpers\CommonHelper;

class CommissionService
{
    private TransactionService $transactionService;
    private ExchangeRateCalculationService $exchangeRateService;

    public function __construct(
        TransactionService             $transactionService,
        ExchangeRateCalculationService $exchangeRateCalculationService
    )
    {
        $this->transactionService = $transactionService;
        $this->exchangeRateService = $exchangeRateCalculationService;
    }

    public function calculate($user_id, $amount, $transaction_type, $transaction_date, $client_type, $currency = 'EUR'): float
    {
        $commissionAbleAmount = $this->getCommissionAbleAmount($user_id, $amount, $transaction_type, $transaction_date, $client_type, $currency);
        $commissionRate = CommonHelper::getCommissionRate($transaction_type, $client_type);
        $commission = ($commissionAbleAmount> 0) ? $commissionAbleAmount * ($commissionRate / 100) : 0.0;
        return CommonHelper::formatCommission($commission);
    }

    public function getCommissionAbleAmount($user_id, $amount, $transaction_type, $transaction_date, $client_type, $currency)
    {
        if (
            in_array($transaction_type, (array)config('commission.free_of_charges.transaction_types'))
            && in_array($client_type, (array)config('commission.free_of_charges.client_types'))
        ) {
            $totalTransactions = $this->transactionService->all()
                    ->where('user_id', $user_id)
                    ->where('date', '>=', CommonHelper::firstDateOfWeek($transaction_date))
                    ->where('date', '<=', CommonHelper::lastDateOfWeek($transaction_date))
                    ->map(function ($item, $key) {
                        return [
                            'amount' => $this->exchangeRateService->calculate($item['amount'], $item['currency'])
                        ];
                    })->sum('amount');

            if ($totalTransactions < config('commission.free_of_charges.amount')) {
                $amountToBeFreeOfCharge = config('commission.free_of_charges.amount') - $totalTransactions;
                $amount = $amount - $this->exchangeRateService->calculate($amountToBeFreeOfCharge, 'EUR', $currency);
            }
        }

        return $amount;
    }
}
