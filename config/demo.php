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
     | DemoSchoolSeeder creates them, all with the same password.
     */
    'accounts' => [
        'Platform administrator' => 'megan.carter@staff.riversideusd.example',
        'School administrator' => 'david.nguyen@staff.riversideusd.example',
        'Teacher' => 'sarah.mitchell@staff.riversideusd.example',
        'Student' => 'ethan.brooks@student.riversideusd.example',
        'Parent' => 'laura.brooks@family.riversideusd.example',
        'Accountant' => 'james.patel@staff.riversideusd.example',
        'Librarian' => 'emily.rodriguez@staff.riversideusd.example',
    ],

    'password' => 'password',

];
