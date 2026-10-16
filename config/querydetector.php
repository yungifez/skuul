<?php

use App\Models\User;
use BeyondCode\QueryDetector\Outputs\Console;
use BeyondCode\QueryDetector\Outputs\Log;
use Spatie\Permission\Models\Permission;

return [
    /*
     * Global and school permissions are intentionally loaded under separate
     * Spatie teams. The detector sees those two lookups as one repeated
     * relation, but they are not a per-record N+1 query.
     */
    'except' => [
        User::class => [
            Permission::class,
            'permissions',
        ],
    ],

    /*
     * A blocking JavaScript alert makes a development warning interrupt the
     * page being inspected. Keep the warning visible in DevTools and logs.
     */
    'output' => [
        Console::class,
        Log::class,
    ],
];
