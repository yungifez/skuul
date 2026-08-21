---
paths:
  - '**'
---

# General

## Quality gates run in CI
.github/workflows/laravel-tests.yml runs Pint, Larastan, `composer audit`, and the test suite. Keep them green: run `vendor/bin/sail bin pint` and `vendor/bin/sail php vendor/bin/phpstan analyse` before finishing. pint.json is the laravel preset with `not_operator_with_successor_space` off (`!$x`, not `! $x`). phpstan-baseline.neon holds ~200 legacy typing errors; fix errors, never extend the baseline. CI checks out yungifez/april-ui beside the app because composer.json requires it from a path repository.

## MySQL binds a foreign key to the index that leads with its column
Adding `index(['academic_year_id', 'position'])` makes MySQL use that index for
the `academic_year_id` foreign key and drop the single-column one. A later
`dropIndex(['academic_year_id', 'position'])` then fails with error 1553.
Write the `down()` method so it drops the foreign key first, or only drops the
extra column and leaves the index to MySQL, which reduces it to the remaining
column. Run the migration down and up once before you finish.

## Provisioning checks the email domain, so tests need a real one
`App\Actions\Identity\ProvisionAccount` validates `email:rfc,dns`. An address at
`example.com` has no MX record and fails inside the container, which shows up as
"The email must be a valid email address" from code that looks unrelated. Use
`fake()->freeEmail()` or a gmail.com address in tests and fixtures that reach
provisioning.

## Two agents cannot share one test database
`RefreshDatabase` drops and rebuilds the schema, so a second suite running at the
same time wipes the first one mid-run. phpunit.xml's `<env>` does not override a
real environment variable, so pass the database on the command line instead:
`docker compose exec -T -e DB_DATABASE=testing_claude laravel.test php artisan test --compact`.
Create the database once with
`docker compose exec -T mysql mysql -uroot -ppassword -e 'create database if not exists testing_claude'`.
