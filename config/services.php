<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
     * A school only sees card payments offered once its keys are set, so a
     * school that has not signed up never picks a way to pay that cannot
     * take the money.
     */
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'endpoint' => env('STRIPE_ENDPOINT', 'https://api.stripe.com/v1'),
    ],

    /*
     * Documents are printed by whichever renderer is set up. With no address
     * here the application prints with its own built-in renderer, so an
     * installation that sets nothing still hands out invoices and timetables.
     */
    'browser_renderer' => [
        'driver' => env('DOCUMENT_RENDERER'),
        'url' => env('BROWSER_RENDERER_URL'),
        'token' => env('BROWSER_RENDERER_TOKEN'),
        'timeout' => (int) env('BROWSER_RENDERER_TIMEOUT', 120),
    ],

];
