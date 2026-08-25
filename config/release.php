<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Recovery targets
    |--------------------------------------------------------------------------
    |
    | These are the pilot release targets. A deployment must not claim release
    | readiness until the owner has approved the retention policy below.
    |
    */
    'rpo_hours' => (int) env('RELEASE_RPO_HOURS', 24),
    'rto_minutes' => (int) env('RELEASE_RTO_MINUTES', 240),

    /*
    |--------------------------------------------------------------------------
    | Data retention approval
    |--------------------------------------------------------------------------
    */
    'retention_policy_version' => (string) env('RELEASE_RETENTION_POLICY_VERSION', '2026-08'),
    'retention_policy_approved' => filter_var(
        env('RELEASE_RETENTION_POLICY_APPROVED', false),
        FILTER_VALIDATE_BOOL,
    ),

    /*
    |--------------------------------------------------------------------------
    | Pilot report set
    |--------------------------------------------------------------------------
    */
    'pilot_report_keys' => [
        'class-list',
        'student-balances',
        'report-cards',
        'transcripts',
    ],
];
