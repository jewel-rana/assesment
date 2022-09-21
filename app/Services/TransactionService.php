<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TransactionService
{
    /**
     * This method save the transaction data to cache memory
     * @param array $data
     * @return void
     */
    public function save(array $data)
    {
        $transactions = Cache::get('transactions') ?? [];
        $transactions[] = $data;
        Cache::put('transactions', $transactions);
    }

    /**
     * Return all transactions from cache
     * @return Collection
     */
    public function all(): Collection
    {
        return collect(Cache::get('transactions'));
    }

    /**
     * This method query from all transactions by defined key value condition
     * @param array $params
     * @return Collection
     */
    public function getBy(array $params): Collection
    {
        $transactions = collect(Cache::get('transactions') ?? []);
        foreach ($params as $key => $value) {
            $transactions = $transactions->where($key, $value);
        }

        return $transactions;
    }

    /**
     * @param string $transactionDate
     * @param int $userId
     * @param string $clientType
     * @param string $transactionType
     * @param float $transactionAmount
     * @param string $currency
     * @return float
     */
    public function import(string $transactionDate, int $userId, string $clientType, string $transactionType, float $transactionAmount, string $currency): float
    {
        $commission = (new CommissionService())->calculate($userId, $transactionAmount, $transactionType, $transactionDate, $clientType, $currency);
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
