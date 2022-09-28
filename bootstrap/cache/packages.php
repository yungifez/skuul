<?php

return [
    'barryvdh/laravel-debugbar' => [
        'providers' => [
            0 => 'Barryvdh\\Debugbar\\ServiceProvider',
        ],
        'aliases' => [
            'Debugbar' => 'Barryvdh\\Debugbar\\Facades\\Debugbar',
        ],
    ],
    'barryvdh/laravel-dompdf' => [
        'providers' => [
            0 => 'Barryvdh\\DomPDF\\ServiceProvider',
        ],
        'aliases' => [
            'Pdf' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
            'PDF' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
        ],
    ],
    'beyondcode/laravel-query-detector' => [
        'providers' => [
            0 => 'BeyondCode\\QueryDetector\\QueryDetectorServiceProvider',
        ],
    ],
    'fruitcake/laravel-cors' => [
        'providers' => [
            0 => 'Fruitcake\\Cors\\CorsServiceProvider',
        ],
    ],
    'jenssegers/agent' => [
        'providers' => [
            0 => 'Jenssegers\\Agent\\AgentServiceProvider',
        ],
        'aliases' => [
            'Agent' => 'Jenssegers\\Agent\\Facades\\Agent',
        ],
    ],
    'jeroennoten/laravel-adminlte' => [
        'providers' => [
            0 => 'JeroenNoten\\LaravelAdminLte\\AdminLteServiceProvider',
        ],
    ],
    'laravel/fortify' => [
        'providers' => [
            0 => 'Laravel\\Fortify\\FortifyServiceProvider',
        ],
    ],
    'laravel/jetstream' => [
        'providers' => [
            0 => 'Laravel\\Jetstream\\JetstreamServiceProvider',
        ],
    ],
    'laravel/sail' => [
        'providers' => [
            0 => 'Laravel\\Sail\\SailServiceProvider',
        ],
    ],
    'laravel/sanctum' => [
        'providers' => [
            0 => 'Laravel\\Sanctum\\SanctumServiceProvider',
        ],
    ],
    'laravel/tinker' => [
        'providers' => [
            0 => 'Laravel\\Tinker\\TinkerServiceProvider',
        ],
    ],
    'livewire/livewire' => [
        'providers' => [
            0 => 'Livewire\\LivewireServiceProvider',
        ],
        'aliases' => [
            'Livewire' => 'Livewire\\Livewire',
        ],
    ],
    'nascent-africa/jetstrap' => [
        'providers' => [
            0 => 'NascentAfrica\\Jetstrap\\JetstrapServiceProvider',
        ],
        'aliases' => [
            'Jetstrap' => 'NascentAfrica\\Jetstrap\\JetstrapFacade',
        ],
    ],
    'nesbot/carbon' => [
        'providers' => [
            0 => 'Carbon\\Laravel\\ServiceProvider',
        ],
    ],
    'nnjeim/world' => [
        'providers' => [
            0 => 'Nnjeim\\World\\WorldServiceProvider',
        ],
        'aliases' => [
            'Country' => 'Nnjeim\\World\\World',
        ],
    ],
    'nunomaduro/collision' => [
        'providers' => [
            0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
        ],
    ],
    'nunomaduro/termwind' => [
        'providers' => [
            0 => 'Termwind\\Laravel\\TermwindServiceProvider',
        ],
    ],
    'spatie/laravel-ignition' => [
        'providers' => [
            0 => 'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
        ],
        'aliases' => [
            'Flare' => 'Spatie\\LaravelIgnition\\Facades\\Flare',
        ],
    ],
    'spatie/laravel-permission' => [
        'providers' => [
            0 => 'Spatie\\Permission\\PermissionServiceProvider',
        ],
    ],
];
