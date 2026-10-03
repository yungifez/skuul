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

    /*
    | Look up the mail domain of a new email address before accepting it.
    | This stops typing mistakes such as "gmial.com", but it needs DNS. Turn
    | it off where the server has no DNS, and in tests, which must not
    | depend on the network.
    */
    'check_email_domains' => (bool) env('CHECK_EMAIL_DOMAINS', true),

    'invitations' => [

        /*
         | The number of hours a new invitation link stays valid.
         */
        'expires_after_hours' => (int) env('ACCOUNT_INVITATION_EXPIRY_HOURS', 72),

    ],

];
