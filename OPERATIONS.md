# Operations

This guide covers the current development branch, which uses Laravel 13.
Older releases have different requirements. Read the release notes before
updating an existing installation.

Commands below run in the application runtime. In local development, replace
`php artisan` with `vendor/bin/sail artisan`, and run Composer and npm through
Sail. Use a production PHP runtime with a web server, workers, and a scheduler.
The committed Sail configuration is for local development.

## 1. What production needs

| Part | Purpose | Notes |
| --- | --- | --- |
| PHP 8.4 or later | Serves the application | Sail and CI use PHP 8.5; check the locked package requirements |
| Queue worker container | Runs mail, reports, and exports | `php artisan queue:work --tries=3` |
| Scheduler | Runs recurring work | Cron entry calling `php artisan schedule:run` every minute |
| MySQL 8 | Application data | Managed database service is recommended |
| Redis | Queue, shared cache, and locks | `QUEUE_CONNECTION=redis`, `CACHE_DRIVER=redis` |
| Persistent storage | Uploaded files and backups | Configure and verify each Laravel filesystem disk |
| SMTP service | Outgoing mail | Set the `MAIL_*` values |

The queue is required. Set `QUEUE_CONNECTION=redis` in production. The `sync`
queue is only for local development.

Install `mysql` and `mysqldump` in the runtime that runs backups. Configure a
real SMTP service and test invitation delivery. Keep storage on a persistent
volume or a configured external disk. S3 needs a compatible Flysystem adapter;
the current dependency list does not include that adapter.

Point the web server at `public/`. Give the application user write access to
`storage/` and `bootstrap/cache/`. Keep `.env`, source files, and `.git` outside
the public directory. Use HTTPS and a process manager for queue workers.

Run `php artisan schedule:run` every minute. All application instances,
workers, and scheduler processes must use the same Redis cache. Scheduled work
uses shared locks, and health checks read scheduler and queue heartbeats there.

## 2. Deploy

For a fresh installation, configure an empty application database, install
the locked dependencies, build the assets, and run:

```sh
php artisan key:generate --no-interaction
php artisan migrate --force --no-interaction
php artisan db:seed --class=WorldSeeder --force --no-interaction
php artisan storage:link --no-interaction
```

Restrict access to the site until you complete the browser installer. Choose
the first administrator credentials, organization, and campus in the installer.
It runs the required production seeders. Leave demo data disabled. Complete the
school setup checklist after signing in. Cache configuration after installation.

### Breaking upgrade from V2

**Moving V2 data to the current development code is a breaking change with data loss in the live database.**
Migrations delete legacy course offerings, unsupported gradebook records and result snapshots, exam results, and grading definitions.
Other migrations remove old class and section columns.
The new model does not automatically receive all this history.

Selected migrations write JSON Lines archives under `storage/app/legacy-v2` before removing data.
Those archives do not provide a complete backup, automatic conversion, or re-import workflow.
Do not assume they contain every removed column or dependent record.
The destructive migrations reject rollback and cannot reconstruct deleted records.

Do not run the migration command below on a live V2 database as a routine update.
First test a specific migration plan on an isolated restored copy.
Keep a verified full database backup, uploaded files, `APP_KEY`, and compatible original code for recovery.
Verify any required historical-data conversion before upgrading.
A successful migration command does not prove that the existing academic history was preserved.

For later releases, follow this deployment sequence:

1. Read the release notes and test the release on a restored copy of the data.
   Build the release artifact and take a verified database and file backup.
2. Put the site in maintenance mode: `php artisan down`.
   Stop workers from writing data while changing the schema.
3. Start the new containers.
4. Install dependencies: `composer install --no-dev --optimize-autoloader --no-interaction`.
   Check the runtime with `composer check-platform-reqs --no-dev`.
5. Build the front end: `npm ci && npm run build`.
6. Run the migrations: `php artisan migrate --force`.
7. Cache the configuration, routes, and views: `php artisan optimize`.
8. Restart the queue workers: `php artisan queue:restart`.
9. Leave maintenance mode: `php artisan up`.
10. Check `/health`. It must answer with HTTP 200.

Set these values before the first deployment:

- `APP_KEY` — generate once on a fresh installation. Losing it makes
  encrypted values unreadable.
- `APP_ENV=production` and `APP_DEBUG=false`.
- `APP_URL` — the public address, with `https`.
- `SESSION_DRIVER=database` and `SESSION_SECURE_COOKIE=true`.

Keep the application database separate from `testing`. The test suite can
replace data in that database. Preserve `APP_KEY` across all updates.

Do not rerun the installer or default database seeder during updates. Run an
additional seeder only when the release notes require it. The production
permission seeder resets permissions on seeded roles; review local role changes
before running it.

### Roll back

1. Keep the site in maintenance mode and stop workers from writing data.
2. Restore the previous release artifact and its locked dependencies and assets.
3. Check whether the previous code supports the migrated schema. If it does
   not, restore the verified database backup and matching uploaded files.
   This discards changes made after that backup.
4. Preserve the original `APP_KEY`, rebuild caches, and restart workers.
5. Run the application checks, leave maintenance mode, and check `/health`.

Do not use a generic `migrate:rollback --step=1` procedure. A release can contain
multiple migrations, and some migrations discard data. Rehearse the recovery
steps for the specific release before deployment.

Write migrations so that the previous release keeps working. Add a column
before you use it, and remove an old column in a later release.

### Update a server that runs from a git checkout

1. Check for a new release: `php artisan skuul:update --check`.
2. Read the release notes at the link that the command shows.
3. Update: `php artisan skuul:update`.

These commands describe the updater in the current development branch.
Older V2 releases use a different implementation.

The command installs the newest published GitHub release of the same major
version. It requests a backup with uploaded files and enables maintenance mode.
It installs locked dependencies, builds assets, migrates, and runs the production
seeders. It caches the application and restarts workers. It stops if the checkout
has tracked local changes. A new major version can need manual steps, so the
command does not install it.

Use it only for release-tag installations. It finds the current version from
the nearest Git tag; it does not prove that a development branch matches a
release. It requires Git, Composer, npm, and working backups. Review seeded role
permissions before updating. Keep backup files outside the Git worktree.

If a step fails, the site stays in maintenance mode. Follow the rollback
procedure above. `--no-backup` skips the built-in backup; use it only after
taking and verifying a separate database and file backup.

Set `SKUUL_UPDATE_REPOSITORY` to install releases from a fork.

## 3. Back up

Two things must be backed up: the database and the uploaded files.

### Take a backup

```bash
php artisan skuul:backup --with-files
```

The command dumps the database, compresses it, encrypts it, and copies it to the
backup disk named by `BACKUP_DISK` and `BACKUP_PATH`. Point that disk at
another account or another region: a backup kept beside the database is not a
backup. The scheduler runs this every day at 01:30.

`--with-files` reads the filesystem disk named by `monitoring.backup.files_disk`.
The current `config/monitoring.php` does not define that setting, and
`BACKUP_FILES_DISK` alone has no effect. Configure the setting to the disk that
contains uploaded files before using this option. Keep the backup disk separate
from the source disk. Until this is configured and tested, take a database
backup with `php artisan skuul:backup` and back up uploads with your storage
provider. Verify both before an update.

### Encryption

Set `BACKUP_KEY` to a long random value, keep it somewhere other than the
backup disk. An encrypted backup cannot be opened without the key.

```bash
php -r 'echo "base64:", base64_encode(random_bytes(32)), "\n";'
```

The application refuses to write an unencrypted backup while
`BACKUP_REQUIRE_ENCRYPTION` is true. An installation that has another way of
locking the files can set it to false.

The backup cipher encrypts each file in chunks and verifies its signature
before decryption. A failed signature check stops recovery.

### How long backups are kept

Everything from the last `BACKUP_KEEP_DAYS` days stays. Older than that, the
first backup of each month stays for `BACKUP_KEEP_MONTHS` months. `skuul:backup`
removes the rest; `--keep-old` leaves them alone.

The application does not delete personal records on a schedule. The pilot
retention policy must be approved before any record-deletion job is enabled.
The proposed default is:

| Record category | Proposed retention | Rule |
| --- | --- | --- |
| Academic history and official reports | 7 years after the learner leaves | Keep revisions and source references |
| Finance and ledger records | 7 years after the financial period closes | Keep posted entries and reversals |
| Audit, access, and permission history | 7 years | Keep append-only events |
| Discipline and safeguarding cases | 7 years after closure | A legal hold overrides deletion |
| Unsubmitted operational drafts | 2 years after last activity | Do not delete while under review |
| Encrypted database and file backups | 30 daily days and 12 monthly points | Controlled by `skuul:backup` |

The data owner must replace these defaults with the required jurisdictional
policy, record the approved version in `RELEASE_RETENTION_POLICY_VERSION`, and
set `RELEASE_RETENTION_POLICY_APPROVED=true`. Until then, the release gate
fails and no destructive retention task is allowed.

### Point-in-time recovery

Keep the database binary logs as well. A daily dump loses the work of a day;
the binary logs cover the hours between dumps.

### Check the backups

`php artisan skuul:check-backup` reads the age of the newest file in the backup
folder. Keep that folder limited to backup files. The command fails when no
backup exists or the newest file is older than `BACKUP_MAX_AGE_HOURS`.
It also fails when no completed restore falls within `BACKUP_REHEARSAL_MAX_AGE_DAYS`.
It writes each failure to the log. The scheduler runs it every day at
07:00. A successful result does not verify file integrity, uploaded file
coverage, or whether the recovery time meets the target.

### Rehearse the restore

Test recovery before relying on a backup.

```bash
php artisan skuul:rehearse-restore
```

The command takes the newest database backup, decrypts it, loads it into the connection
named by `BACKUP_REHEARSAL_CONNECTION`, and counts what came back. It writes
the outcome to `BACKUP_REHEARSAL_PATH` on the backup disk, which is where
`skuul:check-backup` looks. The scheduler runs it every Sunday at 03:00.

Set up the rehearsal database once:

- `BACKUP_REHEARSAL_CONNECTION=rehearsal`
- `BACKUP_REHEARSAL_DATABASE=skuul_rehearsal`

Create the database first. Use a dedicated database user that has access only
to the rehearsal database. Set `BACKUP_REHEARSAL_USERNAME` and
`BACKUP_REHEARSAL_PASSWORD` for that user. Verify the resolved host and database
before running the command. It writes SQL into the target and does not prevent
you from selecting a live connection.

`--check-only` inspects the dump without restoring it. An unset rehearsal
connection also causes inspection only. Neither result counts as a completed
restore for `skuul:check-backup`. The rehearsal checks required table names and
row counts; it does not restore uploaded files or test the application.

### Restore for real

1. Stop the application, or put it in maintenance mode.
2. Test the selected database backup with
   `php artisan skuul:rehearse-restore --file=backups/skuul-YYYY-MM-DD-HHMMSS.sql.gz.enc --into=rehearsal`.
   Keep the rehearsal connection isolated from production.
3. Follow the tested recovery procedure for your database service to replace
   production data. The rehearsal command deletes its temporary SQL file;
   it does not export a decrypted dump for this step.
4. Restore the matching uploaded files with the tested storage recovery procedure.
   The application has no command that restores the files archive. Its `.enc`
   archives use the application backup cipher, not a generic ZIP password.
5. Point the application at the restored data with the production `APP_KEY`.
6. Sign in, open a result page, and open an invoice.
7. Write down the date of the restore and how long it took.

## 4. Watch the system

### Health

`GET /health` checks the database, the cache, the queue, the storage disk, and
the scheduler heartbeat. The queue check requires a worker heartbeat unless
the queue connection is `sync`. Missing heartbeats and heartbeats older than
five minutes fail. It answers HTTP 200 when its checks pass and HTTP 503
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

## 6. Release-readiness gate

Run the configuration gate before a production deployment:

```bash
php artisan skuul:release-readiness
```

It checks that the retention policy has an approval, recovery targets are
recorded, and the pilot report set is registered. The pilot set is:

- class list;
- student balances;
- report cards; and
- transcripts.

The current recovery targets are an RPO of 24 hours and an RTO of 4 hours.
Change them only with an owner-approved operations decision. Before a release,
run the backup and restore checks as well:

```bash
php artisan skuul:release-readiness --check-backups
```

That command requires a recent backup-folder file and a recent record of a
completed database restore. It does not independently verify encryption,
file coverage, application behavior, or measured RPO and RTO.
The rehearsal database must be isolated, disposable, and never the production
database. Record the observed restore time and row checks with the release.
