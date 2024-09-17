<?php

use PHPUnit\Framework\TestCase;
use CommissionsApp\CommissionCalculator;
use CommissionsApp\BinLookUpService;
use CommissionsApp\CurrencyConversionService;

class CommissionCalculatorTest extends TestCase
{
    public function testCalculateCommissionForEur()
    {
        // Mock the BinLookUpService
        $binServiceMock = $this->createMock(BinLookUpService::class);
        $binServiceMock->method('getCountryFromBin')->willReturn('DK'); // Denmark is in the EU

        // Mock the CurrencyConversionService
        $currencyServiceMock = $this->createMock(CurrencyConversionService::class);
        $currencyServiceMock->method('convertToEur')->willReturn(100.00);

        $calculator = new CommissionCalculator($binServiceMock, $currencyServiceMock);
        $commission = $calculator->calculateCommission('45717360', 100.00, 'EUR');

        $this->assertEquals(1.00, $commission);
    }

    public function testCalculateCommissionForNonEur()
    {
        // Mock the BinLookUpService
        $binServiceMock = $this->createMock(BinLookUpService::class);
        $binServiceMock->method('getCountryFromBin')->willReturn('US'); // United States is not in the EU

        // Mock the CurrencyConversionService
        $currencyServiceMock = $this->createMock(CurrencyConversionService::class);
        $currencyServiceMock->method('convertToEur')->willReturn(50.00);

        $calculator = new CommissionCalculator($binServiceMock, $currencyServiceMock);
        $commission = $calculator->calculateCommission('516793', 50.00, 'USD');

        $this->assertEquals(1.00, $commission);
    }

    public function testCalculateCommissionWithMockedConversion()
    {
        // Mock the BinLookUpService
        $binServiceMock = $this->createMock(BinLookUpService::class);
        $binServiceMock->method('getCountryFromBin')->willReturn('LT'); // Lithuania is in the EU

        // Mock the CurrencyConversionService
        $currencyServiceMock = $this->createMock(CurrencyConversionService::class);
        $currencyServiceMock->method('convertToEur')->willReturn(2000.00);

        $calculator = new CommissionCalculator($binServiceMock, $currencyServiceMock);
        $commission = $calculator->calculateCommission('4745030', 2000.00, 'GBP');

        $this->assertEquals(20.00, $commission);
    }
}
