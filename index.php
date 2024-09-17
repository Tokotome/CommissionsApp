<?php

require 'vendor/autoload.php'; 

use CommissionsApp\BinLookUpService;
use CommissionsApp\CurrencyConversionService;
use CommissionsApp\CommissionCalculator;

$binLookupService = new BinLookUpService();
$currencyConversionService = new CurrencyConversionService();
$commissionCalculator = new CommissionCalculator($binLookupService, $currencyConversionService);

$inputFile = $argv[1];
$rows = explode("\n", file_get_contents($inputFile));

foreach ($rows as $row) {
    if (empty(trim($row))) {
        continue;
    }

    $transaction = json_decode($row, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Error: Invalid JSON in input file' . "\n";
        continue;
    }

    $bin = $transaction['bin'] ?? null;
    $amount = $transaction['amount'] ?? null;
    $currency = $transaction['currency'] ?? null;

    if ($bin === null || $amount === null || $currency === null) {
        echo 'Error: Missing transaction fields' . "\n";
        continue;
    }

    try {
        $commission = $commissionCalculator->calculateCommission($bin, $amount, $currency);
        echo number_format($commission, 2) . "\n";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage() . "\n";
    }
}
