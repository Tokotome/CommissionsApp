<?php 

namespace CommissionsApp\Tests;

use CommissionsApp\CurrencyConversionService;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Exception;

class CurrencyConversionServiceTest extends TestCase
{
    private $currencyService;

    protected function setUp(): void
    {
        // Load environment variables from .env file
        // $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        // $dotenv->load();

        // Initialize CurrencyConversionService with values from environment variables
        //$apiKey = $_ENV['EXCHANGE_RATES_API_KEY'];
        //$apiUrl = $_ENV['EXCHANGE_RATES_API_URL'];

        $apiKey = 'db896b52b1e9613666e41e3636285c52';
        $apiUrl = 'https://api.exchangeratesapi.io/latest';

        $this->currencyService = new CurrencyConversionService($apiKey, $apiUrl);
    }

    public function testConvertToEurWithDifferentCurrency()
    {
        // Fetch exchange rate for JPY
        $apiKey = 'db896b52b1e9613666e41e3636285c52';
        $apiUrl ='https://api.exchangeratesapi.io/latest'.'?access_key='.$apiKey;
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);
        $jpyRate = $data['rates']['JPY'] ?? 0;

        // Calculate the expected amount
        $amount = 100;
        $expectedAmount = round($amount / $jpyRate, 2); // Converting to EUR

        // Test conversion with a real API call
        $convertedAmount = $this->currencyService->convertToEur(100, 'JPY');
        $this->assertEquals($expectedAmount, $convertedAmount, 'The converted amount did not match the expected value.');
    }

    public function testConvertToEurWithSameCurrency()
    {
        // Test conversion when the amount is already in EUR
        $convertedAmount = $this->currencyService->convertToEur(100, 'EUR');
        
        // Assert that the amount remains the same when the currency is EUR
        $this->assertEquals(100, $convertedAmount);
    }

    public function testConvertToEurWithInvalidCurrency()
    {
        // Test conversion with an invalid currency
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Exchange rate for XYZ not found");

        $this->currencyService->convertToEur(100, 'XYZ');
    }
}
