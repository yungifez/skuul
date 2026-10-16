# Contributing to Skuul

V3 development uses the `modernization/domain-slices` branch.
V2 is deprecated. V3 intentionally replaces its application structure and data model.
Read the breaking-change warning in [README.md](README.md) before using an existing database.

## Prepare a change

1. Follow the local setup instructions in [README.md](README.md).
2. Read [AGENTS.md](AGENTS.md), `.ai/rules/index.md`, and the rules for the files you will change.
3. Create a branch from the current V3 development branch.
4. Follow sibling files for naming, validation, authorization, and tests.
5. Add or update tests for the changed behavior.
6. Open a pull request against the V3 development branch.

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

## Report a bug

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

## Prepare an open-source release

1. Select the intended commit and release version.
2. Verify that every CI job passes for that commit, including both PHP versions and production boot checks.
3. Confirm that the dependency locks, runtime requirements, README, and published documentation agree.
4. Complete a fresh installation using the documented commands and the browser installer.
5. Check administrator, teacher, learner, and guardian access for the enabled workflows.
6. Prepare release notes that describe changes, known limits, and the affected data.
7. State that V3 is a breaking change from deprecated V2, with data loss during unsupported in-place upgrades.
8. Link the backup and recovery instructions. State that destructive migrations cannot restore deleted data through rollback.
9. Update development checkout examples to the selected release tag when documenting the published release.
10. Create the tag and GitHub release for the verified commit when publication is authorized.

This process prepares an open-source release. It does not configure an operator's production installation.
Each operator follows [OPERATIONS.md](OPERATIONS.md) for deployment, retention, backups, and recovery checks.
Do not treat `skuul:release-readiness` as evidence that every application workflow passed.

Before accepting changes into the release branch, configure required status checks in GitHub branch protection or a ruleset.
Require `Code style, static analysis, and audit`, `Unit and feature tests (PHP 8.4)`, and `Unit and feature tests (PHP 8.5)`.
Run the workflow on GitHub first so those check names are available.
The workflow file defines checks; it does not configure branch protection.
