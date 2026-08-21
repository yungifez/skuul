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

### Database

Take a full dump at least once a day and keep the binary logs for
point-in-time recovery:

```bash
mysqldump --single-transaction --routines --triggers \
  --host="$DB_HOST" --user="$DB_USERNAME" --password="$DB_PASSWORD" \
  "$DB_DATABASE" | gzip > "skuul-$(date +%F-%H%M).sql.gz"
```

Copy the file to the backup disk named by `BACKUP_DISK` and `BACKUP_PATH`.

### Files

Uploaded files live on the storage disk. When the disk is an S3 bucket, turn
on versioning and cross-region replication. When the disk is local, copy
`storage/app` with the database dump.

### Rules

- Encrypt backups at rest and in transfer.
- Keep daily backups for 30 days and monthly backups for 12 months.
- Store the backups in a different account or region from the live database.
- Never keep the only copy on the application server.

### Check the backups

`php artisan skuul:check-backup` reads the newest file on the backup disk. It
fails and writes an error to the log when the newest backup is missing or
older than `BACKUP_MAX_AGE_HOURS`. The scheduler runs it every day at 07:00.

### Restore

Test a restore every quarter in a separate environment. A backup nobody has
restored is not a backup.

1. Start an empty database and an empty storage bucket.
2. Load the dump: `gunzip < skuul-YYYY-MM-DD-HHMM.sql.gz | mysql "$DB_DATABASE"`.
3. Copy the files back to the storage disk.
4. Point a test application at the restored data with the production `APP_KEY`.
5. Sign in, open a result page, and open an invoice.
6. Write down the date of the test and how long it took.

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
