<?php

namespace CommissionsApp;

use Exception;
use Dotenv\Dotenv;

class CurrencyConversionService
{
    private $apiKey;
    private $apiUrl;
    private $httpClient;

    public function __construct($apiKey = null, $apiUrl = null, $httpClient = null)
    {
        // For testing purposes on your side only. Definitely not a good practice to be here. :)
        if ($apiKey === null || $apiUrl === null) {
            // $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            // $dotenv->load();
            
            // $apiKey = $_ENV['EXCHANGE_RATES_API_KEY'] ?? null;
            // $apiUrl = $_ENV['EXCHANGE_RATES_API_URL'] ?? null;

            $apiKey = 'db896b52b1e9613666e41e3636285c52';
            $apiUrl = 'https://api.exchangeratesapi.io/latest';

            if ($apiKey === null || $apiUrl === null) {
                throw new Exception("Environment variables not loaded properly.");
            }
        }

        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        
        

        
        $this->httpClient = $httpClient ?: 'file_get_contents'; // Use default if no client is provided
    }

    public function convertToEur($amount, $currency)
    {
        if ($currency === 'EUR') {
            return $amount;
        }
        
        $rateData = call_user_func($this->httpClient, $this->apiUrl . '?access_key=' . $this->apiKey);
        $rates = @json_decode($rateData, true)['rates'];

        if (!isset($rates[$currency]) || $rates[$currency] == 0) {
            throw new Exception("Exchange rate for $currency not found");
        }

        // Perform the conversion and round to 2 decimal places
        $convertedAmount = $amount / $rates[$currency];
        return round($convertedAmount, 2);
    }
}
