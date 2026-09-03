# Known Bugs

## Repeated queries on setup pages

- Status: Open
- Area: Academic setup and level pages
- Observed: Debugbar reports 52–86 queries, duplicate query groups, and N+1 groups on recent setup requests.
- Impact: Setup pages may become slow as school data grows.
- Resolution so far: The academic structure tree now eager-loads each section's academic year. The remaining duplicate and feature-setting queries still need review.

## Full test suite has unrelated failures

- Status: Open
- Area: Automated QA
- Observed: The baseline full suite completed with 1,270 passed, 129 failed, and 5 skipped tests. Failures included academic calendar screen expectations, academic-period context API mismatches, and other feature-level failures outside the importer and shared edit form.
- Impact: The repository-wide QA baseline is not green.
- Resolution so far: The academic-period context contract, shared user edit rendering, finance fixtures, and academic setup assertions were fixed. Focused suites now pass, but the full suite needs a fresh run before this item can be closed.

## Feature tests cannot run concurrently against the shared testing database

- Status: Open
- Area: Automated QA infrastructure
- Observed: Running multiple PHPUnit files at the same time caused each process to migrate or drop the same MySQL `testing` database. This produced missing-table and table-already-exists errors.
- Impact: Parallel test results are invalid and can leave the testing schema half-migrated.
- Workaround: Reset the `testing` database and run database-refreshing suites sequentially. A separate database per worker or an explicit no-parallel test policy would prevent recurrence.

## User creation requests validated before authorization

- Status: Fixed
- Area: Administrator, parent, teacher, and student provisioning
- Observed: Unauthorized users received validation redirects instead of a forbidden response when submitting malformed create requests.
- Resolution: StoreUserRequest now authorizes each provisioning route before validation; focused administrator, parent, teacher, and student tests cover authorized and unauthorized flows.

## Portal staff-area boundary

- Status: Fixed
- Area: Parent and student portals
- Observed: Portal-only users could reach staff finance and calendar endpoints directly.
- Resolution: Staff policies, navigation, and child-scoped invoice routes now enforce the role boundary; portal screen tests cover normal, alternate, empty, and forbidden flows.

## Duplicate fee invoice posting

- Status: Fixed
- Area: Finance
- Observed: Retrying a fee invoice submission could create duplicate invoices.
- Resolution: Invoice forms send an idempotency key, and the service uses a school-scoped batch record, lock, and atomic transaction; replay coverage verifies no duplicate posting.

## Cloud login logo fallback

- Status: Fixed
- Area: Authentication screen
- Observed: The production login screen rendered a broken logo when the Cloud environment did not define LOGO_PATH.
- Resolution: The application now uses the committed logo asset as its default, with a regression test for the deployable fallback.

## Feature settings validated before authorization

- Status: Fixed
- Area: School feature settings
- Observed: A school member without settings permission reached feature validation before the update policy ran, so malformed submissions returned validation errors instead of forbidden.
- Resolution: Feature settings now authorize in the form request before validating the submitted tool choices, with regression coverage for the forbidden exception flow.

## Attendance and cross-campus portal feature gates

- Status: Fixed
- Area: Attendance register and family portal
- Observed: Attendance routes were reachable after the school disabled Attendance, and portal access checked the current working campus instead of the enrollment's campus.
- Resolution: Attendance routes now use the school feature middleware, and portal access resolves the feature setting from the enrollment campus. Normal and closed-campus tests cover both flows.
