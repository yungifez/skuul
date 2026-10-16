<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        if ($app->configurationIsCached()) {
            throw new RuntimeException('Tests refuse cached configuration. Run vendor/bin/sail artisan config:clear in the local test checkout.');
        }

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
