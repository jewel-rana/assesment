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

    /**
     * @param TransactionService $transactionService
     * @param CommissionService $commissionService
     * @param ExchangeRateCalculationService $exchangeRateCalculationService
     */
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
                $transactionDate = $transaction[0];
                $userId = $transaction[1];
                $clientType = $transaction[2];
                $transactionType = $transaction[3];
                $transactionAmount = (float) $transaction[4];
                $currency = $transaction[5];
                $commission = $this->commissionService->calculate($userId, $transactionAmount, $transactionType, $transactionDate, $clientType, $currency);
                echo $commission . PHP_EOL;
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
