<?php

namespace App\Services\Location;

use Illuminate\Support\Facades\Cache;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\State;
use RuntimeException;

class LocationCatalog
{
    /**
     * Return the states for a country.
     *
     * @return list<string>
     */
    public function statesForCountry(string $countryName): array
    {
        $country = $this->country($countryName);

        if ($country === null) {
            return [];
        }

        return State::query()
            ->where('country_id', $country->id)
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();
    }

    /**
     * Return the cities for a country from the package catalog.
     *
     * The package keeps cities in a 53 MB JSON file. Read it as a stream so
     * selecting a country does not require loading the whole catalog into PHP
     * memory, then cache the small country-specific result.
     *
     * @return list<string>
     */
    public function citiesForCountry(string $countryName): array
    {
        $country = $this->country($countryName);

        if ($country === null) {
            return [];
        }

        return Cache::rememberForever(
            'location-cities-v1-'.strtolower((string) $country->iso2),
            fn (): array => $this->readCitiesForCountry((string) $country->iso2),
        );
    }

    private function country(string $countryName): ?Country
    {
        if (blank($countryName)) {
            return null;
        }

        return Country::query()->where('name', $countryName)->first();
    }

    /**
     * @return list<string>
     */
    private function readCitiesForCountry(string $countryCode): array
    {
        $path = base_path('vendor/nnjeim/world/resources/json/cities.json');
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('The world city catalog could not be opened.');
        }

        $cities = [];
        $record = '';
        $readingRecord = false;

        try {
            while (($line = fgets($handle)) !== false) {
                $trimmedLine = trim($line);

                if (!$readingRecord && $trimmedLine === '{') {
                    $record = $line;
                    $readingRecord = true;

                    continue;
                }

                if (!$readingRecord) {
                    continue;
                }

                $record .= $line;

                if (!in_array($trimmedLine, ['}', '},'], true)) {
                    continue;
                }

                $city = json_decode(rtrim($record, " \t\r\n,"), true, flags: JSON_THROW_ON_ERROR);

                if (strtoupper((string) ($city['country_code'] ?? '')) === strtoupper($countryCode)) {
                    $cities[] = (string) $city['name'];
                }

                $record = '';
                $readingRecord = false;
            }
        } finally {
            fclose($handle);
        }

        $cities = array_values(array_filter(
            array_unique($cities),
            fn (string $city): bool => filled($city),
        ));
        sort($cities, SORT_NATURAL | SORT_FLAG_CASE);

        return $cities;
    }
}
