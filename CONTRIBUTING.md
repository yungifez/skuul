# Contributing to Skuul

V2 is deprecated. V3 intentionally replaces its application structure and data model.
Read the breaking-change warning in [README.md](README.md) before using an existing database.

## Branches

Use `main` as the stable development branch. The demo deploys from `main`.

Create short-lived branches from `main`:

- `feature/<name>` for a new feature.
- `fix/<name>` for a bug fix.
- `docs/<name>` for documentation-only changes.
- `chore/<name>` for maintenance and dependency work.

Do not use a long-lived branch for normal development.

## Prepare a change

1. Follow the local setup instructions in [README.md](README.md).
2. Read [AGENTS.md](AGENTS.md), `.ai/rules/index.md`, and the rules for the files you will change.
3. Create a branch from `main`.
4. Follow sibling files for naming, validation, authorization, and tests.
5. Add or update tests for the changed behavior.
6. Open a pull request against `main`.

Keep a change focused on one problem. Describe the resulting behavior and its validation.
Test permitted access, refused access, and campus ownership when changing a campus workflow.
Do not increase the static-analysis baseline to hide new errors.

## Run local checks

Use Sail for PHP, Artisan, Composer, and Node.js commands.
Tests use the shared `testing` database and can replace its data.
Keep application data in `skuul` or another separate database.
Use `.env.testing` for Artisan commands with `--env=testing`.
Keep the database lock enabled. Do not use `--parallel` against this shared database.
Tests refuse cached application configuration before booting the application.
If this check fails, run `vendor/bin/sail artisan config:clear` in your local development checkout.

Run the affected test file first:

```sh
vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php
```

Replace the example path with the test for your change.
Run the project checks before submitting a pull request:

```sh
vendor/bin/sail composer validate --strict
vendor/bin/sail composer check-platform-reqs
vendor/bin/sail bin pint --dirty --format agent
vendor/bin/sail php vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/sail composer audit
vendor/bin/sail npm audit --audit-level=high
vendor/bin/sail npm run build
```

CI runs the complete suite on PHP 8.4 and 8.5.
It uses an isolated MySQL 8 service and Node.js 24.
It then removes development packages and verifies configuration, route, and view caching.
Check every job in the [Laravel tests workflow](.github/workflows/laravel-tests.yml).

The validation workflow uses a read-only token. Actions are pinned to commit IDs.
The labeler has separate permission to label pull requests. It does not execute pull-request code.
When updating an action, verify the upstream release and commit ID.
Update the labeler configuration when its matching syntax changes.

## Pull requests

Open pull requests against `main`. Use an imperative title under 50 characters.
Explain the change, the reason for the change, and the checks that you ran.

Pull requests are squash-merged. The pull request title becomes the commit title on `main`.
Use a conventional commit prefix in the title when the change should appear in the next release:

- `fix:` for a bug fix and a patch release.
- `feat:` for a backwards-compatible feature and a minor release.
- `feat!:` for a breaking change and a major release.

Other prefixes, such as `docs:`, `test:`, and `chore:`, do not create a release.

## Report a bug

Do not report a vulnerability in a public issue. Follow [SECURITY.md](SECURITY.md).

Use the [bug report template](.github/ISSUE_TEMPLATE/bug_report.md).
Include the application tag or commit, runtime versions, role, and enabled feature.
List steps that reproduce the problem, the expected behavior, and the observed behavior.
Use fictional records in examples and screenshots. Remove credentials and personal data from logs.

For a feature request, describe the role, current task, desired behavior, and a concrete example.

## Update documentation

The documentation source is [yungifez/skuul.org](https://github.com/yungifez/skuul.org).
Current V3 guides are in `current/`. Keep V2 guides separate.
Update affected role instructions and task guides when behavior changes.
Check button labels, permissions, states, download formats, and commands against the code.
Use short sentences and direct instructions.

Build the documentation and run its `docs:check` script.
Use its source-comparison checks when application routes or registered features change.
Follow its screenshot plan for real browser captures. Record missing captures as planned.

## Releases

Pushes to `main` run Release Please. It opens or updates a release pull request with the next version and changelog.
After the release pull request is merged, Release Please creates the version tag and the GitHub release.

Release Please reads the conventional commit prefixes on `main`. A `fix:` commit creates a patch release.
A `feat:` commit creates a minor release. A `feat!:` commit creates a major release.
Do not edit the version, tag, or changelog by hand during a normal release.

Before you merge a release pull request:

1. Verify that every CI job passes on `main`, including both PHP versions and production boot checks.
2. Confirm that the dependency locks, runtime requirements, README, and published documentation agree.
3. Complete a fresh installation using the documented commands and the browser installer.
4. Check administrator, teacher, learner, and guardian access for the enabled workflows.
5. Add upgrade notes to the release pull request when the release has migrations or breaking behavior.
6. Link the backup and recovery instructions. State that destructive migrations cannot restore deleted data through rollback.

Use the manual `Release` workflow only for recovery releases. It tags the current `main` commit after the Laravel tests pass on it.
Do not run it while a release pull request is pending for the same changes.

This process prepares an open-source release. It does not configure an operator's production installation.
Each operator follows [Deployment](https://skuul.org/current/getting-started/deployment) and [Backups and monitoring](https://skuul.org/current/operations) for deployment, retention, backups, and recovery checks.
Do not treat `skuul:release-readiness` as evidence that every application workflow passed.

Before accepting changes into `main`, configure required status checks in GitHub branch protection or a ruleset.
Require `Code style, static analysis, and audit`, `Unit and feature tests (PHP 8.4)`, and `Unit and feature tests (PHP 8.5)`.
Run the workflow on GitHub first so those check names are available.
The workflow file defines checks; it does not configure branch protection.
