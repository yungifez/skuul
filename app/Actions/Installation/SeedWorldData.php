<?php

namespace App\Actions\Installation;

use Database\Seeders\WorldSeeder;
use Illuminate\Support\Facades\Artisan;

class SeedWorldData
{
    /**
     * Load the reference catalog required by the installer.
     */
    public function seed(): void
    {
        if (Artisan::call('db:seed', [
            '--class' => WorldSeeder::class,
            '--force' => true,
        ]) !== 0) {
            throw new \RuntimeException('The country and state reference data could not be installed.');
        }
    }
}
