<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TransactionService
{
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
}
