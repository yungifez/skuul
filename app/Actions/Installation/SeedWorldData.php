<?php

namespace App\Actions\Installation;

use Database\Seeders\WorldSeeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class SeedWorldData
{
    private const REQUIRED_MEMORY_LIMIT = 512 * 1024 * 1024;

    /**
     * Load the reference catalog required by the installer.
     */
    public function seed(): void
    {
        $previousMemoryLimit = $this->raiseMemoryLimitForSeeder();

        try {
            $exitCode = Artisan::call('db:seed', [
                '--class' => WorldSeeder::class,
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                $output = trim(Artisan::output());

                throw new RuntimeException($output === ''
                    ? 'The country and state reference data could not be installed.'
                    : "The country and state reference data could not be installed. Artisan output: {$output}");
            }
        } finally {
            if ($previousMemoryLimit !== null) {
                ini_set('memory_limit', $previousMemoryLimit);
            }
        }
    }

    private function raiseMemoryLimitForSeeder(): ?string
    {
        $currentMemoryLimit = ini_get('memory_limit');

        if ($currentMemoryLimit === '-1'
            || $this->memoryLimitInBytes($currentMemoryLimit) >= self::REQUIRED_MEMORY_LIMIT) {
            return null;
        }

        $previousMemoryLimit = ini_set('memory_limit', '512M');
        $newMemoryLimit = ini_get('memory_limit');

        if ($previousMemoryLimit === false) {
            throw new RuntimeException(
                "The country and state reference data requires a PHP memory limit of at least 512M. The current limit is {$currentMemoryLimit}.",
            );
        }

        if ($this->memoryLimitInBytes($newMemoryLimit) < self::REQUIRED_MEMORY_LIMIT) {
            ini_set('memory_limit', $previousMemoryLimit);

            throw new RuntimeException(
                "The country and state reference data requires a PHP memory limit of at least 512M. The current limit is {$currentMemoryLimit}.",
            );
        }

        return $previousMemoryLimit;
    }

    private function memoryLimitInBytes(string $memoryLimit): int
    {
        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int) $memoryLimit;

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
