---
paths:
  - '**'
  - phpunit.xml
---

# General

## Quality gates run in CI
.github/workflows/laravel-tests.yml runs Pint, Larastan, `composer audit`, and the test suite. Keep them green: run `vendor/bin/sail bin pint` and `vendor/bin/sail php vendor/bin/phpstan analyse` before finishing. pint.json is the laravel preset with `not_operator_with_successor_space` off (`!$x`, not `! $x`). phpstan-baseline.neon holds ~200 legacy typing errors; fix errors, never extend the baseline.

## MySQL binds a foreign key to the index that leads with its column
Adding `index(['academic_year_id', 'position'])` makes MySQL use that index for
the `academic_year_id` foreign key and drop the single-column one. A later
`dropIndex(['academic_year_id', 'position'])` then fails with error 1553.
Write the `down()` method so it drops the foreign key first, or only drops the
extra column and leaves the index to MySQL, which reduces it to the remaining
column. Run the migration down and up once before you finish.

## New email addresses go through new_email_rule()
Validate an email address that is being entered or changed with
`new_email_rule()`, never a literal `email:rfc,dns`. It looks up the mail domain
only when `identity.check_email_domains` (`CHECK_EMAIL_DOMAINS`) is on.
phpunit.xml turns it off, because live DNS made tests fail at random. A test
about the lookup itself turns it on with
`config(['identity.check_email_domains' => true])` and uses a `.invalid`
domain, which fails with or without a network.

## Tests use the shared testing database
Use the testing database for all local and CI test runs. Do not direct tests to
laravel or create alternate testing_* databases. PHPUnit must override both
`<env>` and `<server>` values because Laravel reads `$_SERVER` first. Use
`.env.testing` for Artisan commands with `--env=testing`.
