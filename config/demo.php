<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    |
    | A public demo resets its data every hour and refuses deletions, so one
    | visitor cannot empty the school for the next. Never turn this on for a
    | real school: the reset removes every record.
    |
    */

    'enabled' => (bool) env('DEMO_MODE', false),

    /*
     | The seeded accounts the sign-in page offers, by the role they show.
     | DatabaseSeeder creates them, all with the same password.
     */
    'accounts' => [
        'Platform administrator' => 'super@example.com',
        'School administrator' => 'admin@example.com',
        'Teacher' => 'teacher@example.com',
        'Student' => 'student@example.com',
        'Parent' => 'parent@example.com',
        'Accountant' => 'accountant@example.com',
        'Librarian' => 'libratian@example.com',
    ],

    'password' => 'password',

];
