<?php

namespace App\Console\Commands;

use App\Services\CommissionService;
use App\Services\ExchangeRateCalculationService;
use App\Services\TransactionService;
use Illuminate\Console\Command;

class TransactionImportCommand extends Command
{
    protected $signature = 'transaction:import';

    protected $description = 'Export transaction from csv file';
    private TransactionService $transactionService;
    private CommissionService $commissionService;
    private ExchangeRateCalculationService $exchangeRateCalculationService;

    public function __construct(
        TransactionService             $transactionService,
        CommissionService              $commissionService,
        ExchangeRateCalculationService $exchangeRateCalculationService
    )
    {
        parent::__construct();
        $this->transactionService = $transactionService;
        $this->commissionService = $commissionService;
        $this->exchangeRateCalculationService = $exchangeRateCalculationService;
    }

    public function handle()
    {
        $transactionCsv = fopen(public_path('files/transaction.csv'), 'r');

        if ($transactionCsv !== FALSE) {
            while (($transaction = fgetcsv($transactionCsv, 100, ',')) !== FALSE) {
                $commission = $this->commissionService->calculate((float)$transaction[4], $transaction[3], $transaction[2], $transaction[5]);
                echo $commission . PHP_EOL;
                $this->transactionService->save([
                    'date' => $transaction[0],
                    'user_id' => $transaction[1],
                    'client' => $transaction[2],
                    'transaction_type' => $transaction[3],
                    'amount' => $transaction[4],
                    'currency' => $transaction[5],
                    'commission' => $commission,
                ]);
            }
            fclose($transactionCsv);
        }

        print_r($this->transactionService->all());
    }
}
