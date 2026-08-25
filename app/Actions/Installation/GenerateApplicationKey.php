<?php

namespace App\Actions\Installation;

use Illuminate\Support\Facades\Artisan;

class GenerateApplicationKey
{
    /**
     * Generate the key once. Never rotate an existing application key here.
     */
    public function generate(): void
    {
        if (filled(config('app.key'))) {
            throw new \InvalidArgumentException('The application key already exists. It was not changed.');
        }

        if (Artisan::call('key:generate', ['--force' => true]) !== 0) {
            throw new \RuntimeException('The application key could not be generated.');
        }

        Artisan::call('config:clear');
    }
}
