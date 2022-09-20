<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TransactionService
{
    private CommissionService $commissionService;
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    public function save(array $data)
    {
        $transactions = Cache::get('transactions') ?? [];
        $transactions[] = $data;
        Cache::put('transactions', $transactions);
    }

    public function all(): Collection
    {
        return collect(Cache::get('transactions'));
    }

    public function getBy(array $params): Collection
    {
        $transactions = collect(Cache::get('transactions') ?? []);
        foreach ($params as $key => $value) {
            $transactions->where($key, $value);
        }

        return $transactions;
    }

    public function import($transactionDate, $userId, $clientType, $transactionType, $transactionAmount, $currency): float
    {
        $commission = $this->commissionService->calculate($userId, $transactionAmount, $transactionType, $transactionDate, $clientType, $currency);
        $this->save([
            'date' => $transactionDate,
            'user_id' => $userId,
            'client' => $clientType,
            'transaction_type' => $transactionType,
            'amount' => $transactionAmount,
            'currency' => $currency,
            'commission' => $commission,
        ]);
        return $commission;
    }
}
