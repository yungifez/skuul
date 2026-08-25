<?php

namespace App\Actions\Installation;

use Database\Seeders\WorldSeeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class SeedWorldData
{
    /**
     * Load the reference catalog required by the installer.
     */
    public function seed(): void
    {
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
    }
}
