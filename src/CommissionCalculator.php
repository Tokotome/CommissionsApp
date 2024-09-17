<?php 

namespace CommissionsApp;

class CommissionCalculator
{
    private $binLookupService;
    private $currencyConversionService;

    public function __construct($binLookupService, $currencyConversionService)
    {
        $this->binLookupService = $binLookupService;
        $this->currencyConversionService = $currencyConversionService;
    }

    public function calculateCommission($bin, $amount, $currency)
    {
        $countryCode = $this->binLookupService->getCountryFromBin($bin);
        $isEu = $this->isEu($countryCode);

        $amountInEur = $this->currencyConversionService->convertToEur($amount, $currency);

        $commissionRate = $isEu ? 0.01 : 0.02;
        $commission = $amountInEur * $commissionRate;

        return ceil($commission * 100) / 100; // Ceiling the commission to the nearest cent
    }

    private function isEu($countryCode)
    {
        $euCountries = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
        return in_array($countryCode, $euCountries);
    }
}

?>