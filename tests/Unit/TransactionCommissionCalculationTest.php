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
        if (!$this->app) {
            $this->refreshApplication();
        }
        $this->commissionService = new CommissionService(new TransactionService(), new ExchangeRateCalculationService());
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
     * @dataProvider dataProviderForAddTesting
     */
    public function test_commission_fees(string $date, int $userId, string $clientType, string $transactionType, float $amount, string $currency, float $expectation)
    {
        $commission = $this->commissionService->calculate($userId, $amount, $transactionType, $date, $clientType, $currency);
        $this->assertEquals(
            $expectation,
            $commission
        );
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            ['2014-12-31', 4, 'private', 'withdraw', 1200.00, 'EUR', 0.60]
        ];
    }
}
