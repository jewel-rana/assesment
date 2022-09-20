<?php

namespace App\Console\Commands;

use App\Services\CommissionService;
use App\Services\TransactionService;
use Illuminate\Console\Command;

class TransactionImportCommand extends Command
{
    protected $signature = 'transaction:import {file}';

    protected $description = 'Export transaction from csv file';
    private TransactionService $transactionService;
    private CommissionService $commissionService;

    /**
     * @param TransactionService $transactionService
     * @param CommissionService $commissionService
     */
    public function __construct(
        TransactionService             $transactionService,
        CommissionService              $commissionService
    )
    {
        parent::__construct();
        $this->transactionService = $transactionService;
        $this->commissionService = $commissionService;
    }

    public function handle()
    {
        $transactionCsv = fopen(public_path('files/' . $this->argument('file')), 'r');

        if ($transactionCsv !== FALSE) {
            while (($transaction = fgetcsv($transactionCsv, 100, ',')) !== FALSE) {
                $transactionDate = $transaction[0];
                $userId = $transaction[1];
                $clientType = $transaction[2];
                $transactionType = $transaction[3];
                $transactionAmount = (float) $transaction[4];
                $currency = $transaction[5];
                $commission = $this->commissionService->calculate($userId, $transactionAmount, $transactionType, $transactionDate, $clientType, $currency);
                echo number_format($commission, 2) . PHP_EOL;
                $this->transactionService->save([
                    'date' => $transactionDate,
                    'user_id' => $userId,
                    'client' => $clientType,
                    'transaction_type' => $transactionType,
                    'amount' => $transactionAmount,
                    'currency' => $currency,
                    'commission' => $commission,
                ]);
            }
            fclose($transactionCsv);
        }

//        print_r($this->transactionService->all());
    }
}
