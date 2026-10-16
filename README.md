# Skuul

Skuul is a school management application. It manages organizations and campuses,
student records, academic calendars, attendance, gradebooks, timetables, fees,
and reports. It also supports family access, student care, staff operations,
programmes, facilities, boarding, and library records.

## Documentation

The documentation site is [skuul.org](https://skuul.org/).
Its source is [yungifez/skuul.org](https://github.com/yungifez/skuul.org).
A live demo runs at [demo.skuul.org](https://demo.skuul.org/).
The guides cover installation, campus setup, user workflows, family access,
operations, and developer reference.

V3 is being prepared for release. This branch uses Laravel 13 and Livewire 4. Use the **V3**
guides for this branch. V2 is deprecated. V3 intentionally replaces its application structure and data model.
The V2 guides describe older releases. Read
[What's new in V3](https://skuul.org/current/whats-new) and the
[release notes](https://github.com/yungifez/skuul/releases) before installing
or updating a released version.

> **Breaking change: data loss when upgrading from V2.**
> The development migrations delete legacy academic records and remove schema columns.
> They do not automatically convert all V2 history. Selected JSON archives do not replace a full database backup.
> Do not run this as a routine in-place V2 update. Test a specific migration and recovery plan first.
> The destructive migrations cannot restore deleted data through rollback.
> Read [Updating](https://skuul.org/current/getting-started/updating#breaking-upgrade-effects) before migrating an existing installation.

## Requirements

- PHP 8.4.1 or later, as required by the locked packages. Sail uses PHP 8.5; CI tests PHP 8.4 and 8.5.
- Composer 2.
- Node.js 24 and npm, matching Sail and CI.
- MySQL 8 and Redis.
- Docker with Compose for local development with Sail.
- MySQL client tools for the backup and restore commands.

Use the committed dependency locks. Run `composer check-platform-reqs` in the
target runtime to check its PHP version and extensions.

## Local setup

These commands use the current V3 development branch. Use a release tag when installing a published release.

1. Clone the development branch and enter its directory:

   ```sh
   git clone --branch modernization/domain-slices https://github.com/yungifez/skuul.git skuul
   cd skuul
   ```

2. Install the locked Composer dependencies with PHP 8.4.1 or later and Composer 2:

   ```sh
   composer install --no-interaction --prefer-dist
   ```

   Sail is available after this first install.
3. Copy `.env.example` to `.env`. The example uses `DB_DATABASE=skuul`.
   Keep `testing` for the test suite. Sail creates both databases on a new volume.
   Changing the database name on an existing volume does not create that database or grant access.

   ```sh
   cp .env.example .env
   ```

4. Start Sail and prepare the application:

   ```sh
   vendor/bin/sail up -d
   vendor/bin/sail composer check-platform-reqs
   vendor/bin/sail npm ci
   vendor/bin/sail npm run build
   vendor/bin/sail artisan key:generate --no-interaction
   vendor/bin/sail artisan migrate --no-interaction
   vendor/bin/sail artisan db:seed --class=WorldSeeder --no-interaction
   vendor/bin/sail artisan storage:link --no-interaction
   vendor/bin/sail open
   ```

5. Complete the browser installer. Choose your administrator email and
   password, organization, and first campus. The installer loads the required
   roles and permissions. It does not use a default administrator password.
6. Sign in and complete the school setup checklist. Choose the school year,
   reporting periods, teaching approach, classes, and grading scales.

Load demo data only in a disposable installation. Do not run the default
database seeder against a production database.

Run these commands in separate terminals for background work:

```sh
vendor/bin/sail artisan queue:work --tries=3
vendor/bin/sail artisan schedule:work
```

See [Deployment](https://skuul.org/current/getting-started/deployment), [Updating](https://skuul.org/current/getting-started/updating),
and [Backups and monitoring](https://skuul.org/current/operations) for production setup, updates, backups,
restore checks, and monitoring.

## Development checks

Tests use the `testing` MySQL database and can replace its data. Keep local
application data in a separate database.
Use `.env.testing` for Artisan commands with `--env=testing`.
Do not run tests in parallel against this shared database or bypass its test lock.
Tests refuse cached application configuration because it can override the test database settings.
If this check fails, run `vendor/bin/sail artisan config:clear` in your local development checkout.

```sh
vendor/bin/sail composer validate --strict
vendor/bin/sail composer check-platform-reqs
vendor/bin/sail bin pint --dirty --format agent
vendor/bin/sail artisan test --compact
vendor/bin/sail php vendor/bin/phpstan analyse --memory-limit=2G
vendor/bin/sail composer audit
vendor/bin/sail npm audit --audit-level=high
vendor/bin/sail npm run build
```

CI runs the full suite on PHP 8.4 and 8.5 with MySQL 8 and Node.js 24.
It checks formatting, static analysis, dependency locks, platform requirements, and dependency security.
It also verifies configuration, route, and view caching without development packages.
The [Laravel tests workflow](.github/workflows/laravel-tests.yml) defines these checks.

Follow the project instructions in
[AGENTS.md](AGENTS.md) and the rules in `.ai/rules` when changing code.

## Contributing and releases

Read [CONTRIBUTING.md](CONTRIBUTING.md) for test requirements, bug reports, documentation changes, and release preparation.
Open-source publication requires passing quality checks and clear breaking-change notes.
Each operator must complete the deployment and recovery checks in [Backups and monitoring](https://skuul.org/current/operations) for their installation.

## License

Skuul uses the [MIT license](LICENSE). The original project was inspired by
[4jean/lavSMS](https://github.com/4jean/lavSMS).
