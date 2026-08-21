<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Account Invitations
    |--------------------------------------------------------------------------
    |
    | Administrators provision accounts. The person then receives a one-time
    | link to set a password and sign in. These options control that link.
    |
    */

    'invitations' => [

        /*
         | The number of hours a new invitation link stays valid.
         */
        'expires_after_hours' => (int) env('ACCOUNT_INVITATION_EXPIRY_HOURS', 72),

    ],

];
