<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Slow queries
    |--------------------------------------------------------------------------
    |
    | A query that runs longer than this many milliseconds is written to the
    | log with its SQL and its bindings. Raise the number on a busy server to
    | keep the log readable.
    |
    */

    'slow_query_ms' => (int) env('MONITORING_SLOW_QUERY_MS', 500),

    /*
    |--------------------------------------------------------------------------
    | Slow requests
    |--------------------------------------------------------------------------
    |
    | Log the request when the database work of one request takes longer than
    | this many milliseconds in total.
    |
    */

    'slow_request_query_ms' => (int) env('MONITORING_SLOW_REQUEST_QUERY_MS', 2000),

    /*
    |--------------------------------------------------------------------------
    | Backups
    |--------------------------------------------------------------------------
    |
    | `skuul:check-backup` reads these values. It reports a failure when the
    | newest file in the backup folder is older than `max_age_hours`.
    |
    */

    'backup' => [
        'disk' => env('BACKUP_DISK', 'local'),
        'path' => env('BACKUP_PATH', 'backups'),
        'max_age_hours' => (int) env('BACKUP_MAX_AGE_HOURS', 26),
    ],

];
