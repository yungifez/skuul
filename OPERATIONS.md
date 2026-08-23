# Operations

This guide explains how to deploy Skuul, how to keep backups, and how to see
that the system is healthy. It is provider-neutral. Any host that runs
containers, MySQL, Redis, and object storage can run the application.

## 1. What production needs

| Part | Purpose | Notes |
| --- | --- | --- |
| PHP 8.5 web container | Serves the application | Built from the image in `docker-compose.yml` |
| Queue worker container | Runs mail, reports, and exports | `php artisan queue:work --tries=3` |
| Scheduler | Runs recurring work | Cron entry calling `php artisan schedule:run` every minute |
| MySQL 8 | Application data | Managed database service is recommended |
| Redis | Queue, cache, and locks | `QUEUE_CONNECTION=redis` |
| Object storage | Uploaded files | S3-compatible bucket, `FILESYSTEM_DISK=s3` |
| SMTP service | Outgoing mail | Set the `MAIL_*` values |

The queue is required. Set `QUEUE_CONNECTION=redis` in production. The `sync`
queue is only for local development.

## 2. Deploy

Run these steps for every release.

1. Check out the active April UI `main` branch beside this repository.
2. Build the image and push it to your registry.
3. Put the site in maintenance mode: `php artisan down`.
4. Start the new containers.
5. Install dependencies: `composer install --no-dev --optimize-autoloader`.
6. Build the front end: `npm ci && npm run build`.
7. Run the migrations: `php artisan migrate --force`.
8. Cache the configuration, routes, and views: `php artisan optimize`.
9. Restart the queue workers: `php artisan queue:restart`.
10. Leave maintenance mode: `php artisan up`.
11. Check `/health`. It must answer with HTTP 200.

The application uses a path repository. The release checkout must contain the
current April UI `main` branch beside the application before Composer runs.

Set these values before the first deployment:

- `APP_KEY` — generate once with `php artisan key:generate`. Losing it makes
  encrypted values unreadable.
- `APP_ENV=production` and `APP_DEBUG=false`.
- `APP_URL` — the public address, with `https`.
- `SESSION_DRIVER=database` and `SESSION_SECURE_COOKIE=true`.

Create the first platform administrator with
`php artisan skuul:create-super-admin`. Run only the production seeders. Demo
data is separate and must never run on a live database.

### Roll back

1. Start the previous image.
2. Run `php artisan migrate:rollback --step=1` only when the release added a
   migration that the previous code cannot use.
3. Restart the workers and check `/health`.

Write migrations so that the previous release keeps working. Add a column
before you use it, and remove an old column in a later release.

## 3. Back up

Two things must be backed up: the database and the uploaded files.

### Take a backup

```bash
php artisan skuul:backup --with-files
```

The command dumps the database, squeezes it, locks it, and copies it to the
backup disk named by `BACKUP_DISK` and `BACKUP_PATH`. Point that disk at
another account or another region: a backup kept beside the database is not a
backup. The scheduler runs this every day at 01:30.

`--with-files` copies the disk named by `BACKUP_FILES_DISK` as well, because a
database without the files it names is only half a school.

### Locking

Set `BACKUP_KEY` to a long random value, keep it somewhere other than the
backup disk, and never lose it: a locked backup cannot be opened without it.

```bash
php -r 'echo "base64:", base64_encode(random_bytes(32)), "\n";'
```

The application refuses to write a plain backup while
`BACKUP_REQUIRE_ENCRYPTION` is true. An installation that has another way of
locking the files can set it to false.

Each file is sealed a piece at a time and signed as a whole, so a file that was
changed on the way is refused rather than half-restored.

### How long backups are kept

Everything from the last `BACKUP_KEEP_DAYS` days stays. Older than that, the
first backup of each month stays for `BACKUP_KEEP_MONTHS` months. `skuul:backup`
removes the rest; `--keep-old` leaves them alone.

### Point-in-time recovery

Keep the database binary logs as well. A daily dump loses the work of a day;
the binary logs cover the hours between dumps.

### Check the backups

`php artisan skuul:check-backup` reads the newest file on the backup disk. It
fails and writes an error to the log when the newest backup is missing or
older than `BACKUP_MAX_AGE_HOURS`, or when nobody has rehearsed a restore for
longer than `BACKUP_REHEARSAL_MAX_AGE_DAYS`. The scheduler runs it every day at
07:00.

### Rehearse the restore

A backup nobody has restored is not a backup.

```bash
php artisan skuul:rehearse-restore
```

The command takes the newest backup, unlocks it, loads it into the connection
named by `BACKUP_REHEARSAL_CONNECTION`, and counts what came back. It writes
the outcome to `BACKUP_REHEARSAL_PATH` on the backup disk, which is where
`skuul:check-backup` looks. The scheduler runs it every Sunday at 03:00.

Set up the rehearsal database once:

- `BACKUP_REHEARSAL_CONNECTION=rehearsal`
- `BACKUP_REHEARSAL_DATABASE=skuul_rehearsal`

Restoring writes over whatever is in that database, so give it one nothing else
uses. `--check-only` looks inside the backup without restoring it, which is
what to run where no rehearsal database exists.

### Restore for real

1. Stop the application, or put it in maintenance mode.
2. Bring the backup down and unlock it:
   `php artisan skuul:rehearse-restore --file=backups/skuul-YYYY-MM-DD-HHMMSS.sql.gz.enc --into=rehearsal`
   to prove the file first.
3. Load the same dump into the live database.
4. Copy the files archive back to the storage disk.
5. Point the application at the restored data with the production `APP_KEY`.
6. Sign in, open a result page, and open an invoice.
7. Write down the date of the restore and how long it took.

## 4. Watch the system

### Health

`GET /health` checks the database, the cache, the queue, the storage disk, and
the scheduler heartbeat. It answers HTTP 200 when everything works and HTTP 503
when a check fails. Point your uptime monitor at it. `/up` stays as the simple
framework check.

### Slow work

`App\Providers\MonitoringServiceProvider` writes a warning for:

- Any query slower than `MONITORING_SLOW_QUERY_MS` milliseconds.
- Any request whose database work passes `MONITORING_SLOW_REQUEST_QUERY_MS`.

Raise the values on a busy server. Alert on the count of these warnings, not
on each one.

### Failed jobs

A job the queue gives up on is written to the log as `Queue job failed` and
kept in the `failed_jobs` table. Read them with `php artisan queue:failed` and
run them again with `php artisan queue:retry all`. The scheduler removes
records older than 14 days.

### Audit records

The `audit_events` table holds role changes, permission changes, account state
changes, enrollment state changes, period closures, and result publication.
The table is append-only. Keep it for as long as your retention policy
requires; it is the record of who changed access and results.

## 5. Continuous integration

`.github/workflows/laravel-tests.yml` runs on every push and pull request:

- `vendor/bin/pint --test` for code style.
- `vendor/bin/phpstan analyse` for static analysis, against the baseline.
- `composer audit` for dependency vulnerabilities.
- `php artisan test` for the unit and feature tests.

A pull request must pass all four before it is merged.
