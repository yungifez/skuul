# Skuul

Skuul is a school management application. It manages organizations and campuses,
student records, academic calendars, attendance, gradebooks, timetables, fees,
and reports. It also supports family access, student care, staff operations,
programmes, facilities, boarding, and library records.

## Documentation

The documentation site is [yungifez.github.io/skuul.org](https://yungifez.github.io/skuul.org/).
Its source is [yungifez/skuul.org](https://github.com/yungifez/skuul.org).
The guides cover installation, campus setup, user workflows, family access,
operations, and developer reference.

This branch uses Laravel 13 and Livewire 4. Use the **Current development**
guides for this branch. The V2 guides describe older releases. Check the
[release notes](https://github.com/yungifez/skuul/releases) before installing
or updating a released version.

> **Breaking change: data loss when upgrading from V2.**
> The development migrations delete legacy academic records and remove schema columns.
> They do not automatically convert all V2 history. Selected JSON archives do not replace a full database backup.
> Do not run this as a routine in-place V2 update. Test a specific migration and recovery plan first.
> The destructive migrations cannot restore deleted data through rollback.
> Read [OPERATIONS.md](OPERATIONS.md#breaking-upgrade-from-v2) before migrating an existing installation.

## Requirements

- PHP 8.4 or later. Sail and CI use PHP 8.5.
- Composer 2.
- Node.js and npm. Sail provides Node.js 24; CI currently uses Node.js 20.
- MySQL 8 and Redis.
- Docker with Compose for local development with Sail.
- MySQL client tools for the backup and restore commands.

Use the committed dependency locks. Run `composer check-platform-reqs` in the
target runtime to check its PHP version and extensions.

## Local setup

1. Clone this repository and enter its directory.
2. Install the locked Composer dependencies with a compatible PHP runtime.
   Sail is available after this first install.
3. Copy `.env.example` to `.env`. Set `DB_DATABASE=skuul` before starting a
   new Sail database. Keep `testing` for the test suite.
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

See [OPERATIONS.md](OPERATIONS.md) for production setup, updates, backups,
restore checks, and monitoring.

## Development checks

Tests use the `testing` MySQL database and can replace its data. Keep local
application data in a separate database.

```sh
vendor/bin/sail artisan test --compact
vendor/bin/sail php vendor/bin/phpstan analyse --memory-limit=2G
vendor/bin/sail composer audit
vendor/bin/sail npm run build
```

CI also checks PHP formatting with Pint. Follow the project instructions in
[AGENTS.md](AGENTS.md) and the rules in `.ai/rules` when changing code.

## License

Skuul uses the [MIT license](LICENSE). The original project was inspired by
[4jean/lavSMS](https://github.com/4jean/lavSMS).
