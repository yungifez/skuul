# Known Bugs

## Portal links ignored disabled top-level school tools

- Status: Fixed
- Area: Parent and learner portal
- Observed: Disabling Library on the live School features screen changed the summary to 9 of 10 tools and made the administrator Library route return 404, but the parent overview still rendered two Library links.
- Impact: Families could see a link to a school service that the school had turned off. The direct portal route was also not consistently gated by the top-level feature.
- Reproduction: Turn off Library at `/dashboard/schools/features`, then open `/dashboard/portal/overview` as a guardian with two enrollments.
- Resolution: `PortalAccess::areaIsOpen()` now requires the matching top-level Attendance, Events, Library, or Boarding feature before it evaluates the portal-area setting. Regression coverage covers the overview link, direct Library route, and all four mapped features. Production verification disabled and restored each feature: links disappeared, direct routes returned 404, and all routes returned after restoration. The focused PHPUnit process remains queued behind a pre-existing stalled run.

## Repeated queries on setup pages

- Status: Fixed
- Area: Academic setup and level pages
- Observed: Debugbar reported 52–86 queries, duplicate query groups, and N+1 groups on setup requests. The sidebar alone repeated feature-setting reads for each optional tool.
- Impact: Setup pages could become slow as school data grew.
- Resolution: The academic structure tree eager-loads each section's academic year, and `FeatureManager` now loads applicable school and platform settings once per request with school settings taking precedence. The sidebar regression test measured 16 feature-setting queries before the fix and at most 2 after it.

## Full test suite baseline failures

- Status: Fixed
- Area: Automated QA
- Observed: The baseline full suite completed with 1,270 passed, 129 failed, and 5 skipped tests. Failures included academic calendar screen expectations, academic-period context API mismatches, and other feature-level failures outside the importer and shared edit form.
- Impact: The repository-wide QA baseline was not green, so feature changes could not be trusted across the application.
- Resolution: The academic-period context contract, shared user edit rendering, finance fixtures, school setup assertions, and current route/label expectations were fixed. A fresh isolated run completed with 1,455 tests, 4,393 assertions, zero failures, zero errors, and five explicit skips.

## Feature tests cannot run concurrently against the shared testing database

- Status: Fixed
- Area: Automated QA infrastructure
- Observed: Running multiple PHPUnit files at the same time caused each process to migrate or drop the same MySQL `testing` database. This produced missing-table and table-already-exists errors.
- Impact: Parallel test results are invalid and can leave the testing schema half-migrated.
- Resolution: PHPUnit now acquires a process-wide lock in `tests/bootstrap.php`, so concurrent agent runs wait and execute sequentially against the shared `testing` database. PHPUnit child processes do not reacquire their parent’s lock, so separate-process tests cannot deadlock. The lock is released automatically when the process exits.
