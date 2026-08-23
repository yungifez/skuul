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

        /*
         * A backup holds every mark, every fee, and every safeguarding note
         * the school has, and it is kept away from this machine on purpose.
         * It therefore leaves locked. Without a key `skuul:backup` refuses,
         * unless the installation says plainly that it accepts plain files.
         */
        'key' => env('BACKUP_KEY'),
        'require_encryption' => (bool) env('BACKUP_REQUIRE_ENCRYPTION', true),

        /*
         * Everything from the last `keep_days` days stays. Older than that,
         * the first backup of each month stays for `keep_months` months.
         */
        'keep_days' => (int) env('BACKUP_KEEP_DAYS', 30),
        'keep_months' => (int) env('BACKUP_KEEP_MONTHS', 12),

        /*
         * `skuul:rehearse-restore` loads the newest backup into this
         * connection and looks at what came back. It must be a database
         * nobody else uses: restoring writes over whatever is there.
         */
        'rehearsal' => [
            'connection' => env('BACKUP_REHEARSAL_CONNECTION'),
            'path' => env('BACKUP_REHEARSAL_PATH', 'restore-rehearsals'),
            'max_age_days' => (int) env('BACKUP_REHEARSAL_MAX_AGE_DAYS', 100),
        ],
    ],

];
