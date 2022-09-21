<?php

namespace Tests\Unit;

use App\Services\CommissionService;
use App\Services\ExchangeRateCalculationService;
use App\Services\TransactionService;
use Tests\TestCase;

class TransactionCommissionCalculationTest extends TestCase
{
    private CommissionService $commissionService;

    /**
     * @return Void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->commissionService = new CommissionService();
    }

    /**
     * @param string $date
     * @param int $userId
     * @param string $clientType
     * @param string $transactionType
     * @param float $amount
     * @param string $currency
     * @param float $expectation
     *
     * @dataProvider dataProviderForDataProvider
     */
    public function test_commission_fees(string $date, int $userId, string $clientType, string $transactionType, float $amount, string $currency, float $expectation)
    {
        $commission = $this->commissionService->calculate($userId, $amount, $transactionType, $date, $clientType, $currency);
        $this->assertEquals(
            $expectation,
            $commission
        );
    }

    /**
     * @param string $date
     * @param int $userId
     * @param string $clientType
     * @param string $transactionType
     * @param float $amount
     * @param string $currency
     * @param float $expectation
     *
     * @dataProvider dataProviderForDataProvider
     */
    public function test_transaction_import(string $date, int $userId, string $clientType, string $transactionType, float $amount, string $currency, float $expectation)
    {
        $commission = (new TransactionService())->import($date, $userId, $clientType, $transactionType, $amount, $currency);
        $this->assertEquals(
            $expectation,
            $commission
        );
    }

    public function dataProviderForDataProvider(): array
    {
        return [
            ['2014-12-31', 4, 'private', 'withdraw', 1200.00, 'EUR', 0.60],
            ['2015-01-01', 4, 'private', 'withdraw', 1000.00, 'EUR', 0.00],
            ['2016-01-05', 4, 'private', 'withdraw', 1000.00, 'EUR', 0.00],
            ['2016-01-05', 1, 'private', 'deposit', 200.00, 'EUR', 0.06],
            ['2016-01-06', 2, 'business', 'withdraw', 300.00, 'EUR', 1.50],
            ['2016-01-06', 1, 'private', 'withdraw', 30000, 'JPY', 0.00],
            ['2016-01-07', 1, 'private', 'withdraw', 1000.00, 'EUR', 0.0],
            ['2016-01-07', 1, 'private', 'withdraw', 100.00, 'USD', 0.00],
            ['2016-01-10', 1, 'private', 'withdraw', 100.00, 'EUR', 0.00],
            ['2016-01-10', 2, 'business', 'deposit', 10000.00, 'EUR', 3.00],
            ['2016-01-10', 3, 'private', 'withdraw', 1000.00, 'EUR', 0.00],
            ['2016-02-15', 1, 'private', 'withdraw', 300.00, 'EUR', 0.00],
            ['2016-02-19', 5, 'private', 'withdraw', 3000000, 'JPY', 8607.39]
        ];
    }
}
