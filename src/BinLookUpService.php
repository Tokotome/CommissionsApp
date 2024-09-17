<?php
namespace CommissionsApp;

use Exception;

class BinLookUpService
{
    private $cacheFile = __DIR__ . '/../bin_cache.json';

    public function getCountryFromBin($bin)
    {
        // Check if cache file exists, if not create an empty one
        if (!file_exists($this->cacheFile)) {
            file_put_contents($this->cacheFile, json_encode([]));
        }

        // Load the cache data from file
        $cache = json_decode(file_get_contents($this->cacheFile), true);

        // If the BIN is already in cache, return the cached result
        if (isset($cache[$bin])) {
            return $cache[$bin];
        }

        // Make the API request if not found in cache
        $binData = @file_get_contents('https://lookup.binlist.net/' . $bin);
        if ($binData === false) {
            throw new Exception('BIN lookup failed');
        }

        $binResult = json_decode($binData);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decoding BIN lookup response');
        }

        $countryCode = $binResult->country->alpha2 ?? null;

        // Store the result in the cache array
        $cache[$bin] = $countryCode;

        // Save the updated cache to the file
        file_put_contents($this->cacheFile, json_encode($cache));

        return $countryCode;
    }
}
