<?php

namespace App\Console\Commands;

use App\Services\TransactionService;
use Illuminate\Console\Command;

class TransactionImportCommand extends Command
{
    protected $signature = 'transaction:import {file}';

    protected $description = 'Export transaction from csv file';
    private TransactionService $transactionService;

    /**
     * @param TransactionService $transactionService
     */
    public function __construct(
        TransactionService             $transactionService
    )
    {
        parent::__construct();
        $this->transactionService = $transactionService;
    }

    public function handle()
    {
        $transactionCsv = fopen(public_path('files/' . $this->argument('file')), 'r');

        dd($transactionCsv);

        if ($transactionCsv !== FALSE) {
            while (($transaction = fgetcsv($transactionCsv, 100, ',')) !== FALSE) {
                $transactionDate = $transaction[0];
                $userId = $transaction[1];
                $clientType = $transaction[2];
                $transactionType = $transaction[3];
                $transactionAmount = (float) $transaction[4];
                $currency = $transaction[5];
                $commission = $this->transactionService->import($transactionDate, $userId, $clientType, $transactionType, $transactionAmount, $currency);

                echo $commission . PHP_EOL;
            }
            fclose($transactionCsv);
        }
    }
}
