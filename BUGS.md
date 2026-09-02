# Known Bugs

## Student import fails when `gender` is omitted

- Status: Fixed
- Area: Student import
- Observed: A CSV without a `gender` column passed validation. The apply step then failed with `Undefined array key "gender"` for every row.
- Impact: The import wrote zero student records.
- Expected: `gender` is optional, or the importer rejects the file during validation.
- Resolution: `gender` is now listed as optional, and the importer stores `null` when it is missing. A regression test covers the import path.

## Static analysis errors remain

- Status: Fixed
- Area: Application type safety
- Observed: Larastan reports 16 errors in finance actions, report building, fee views and policies. The errors include unnecessary nullsafe access and undefined properties.
- Impact: Static QA does not pass.
- Resolution: Finance actor and payment-period access now use explicit nullable branches, `ReportRun` declares its academic-period attribute, fee relationships have concrete return types, stale baseline suppressions were removed, and fee-invoice serialization handles missing enrollments explicitly. Larastan now reports no errors.

## Repeated queries on setup pages

- Status: Open
- Area: Academic setup and level pages
- Observed: Debugbar reports 52–86 queries, duplicate query groups, and N+1 groups on recent setup requests.
- Impact: Setup pages may become slow as school data grows.
- Resolution so far: The academic structure tree now eager-loads each section's academic year. The remaining duplicate and feature-setting queries still need review.

## User profile and edit screens can fail on birthday rendering

- Status: Fixed
- Area: User profile and edit screens
- Observed: The `User` model exposes `birthday` as a formatted string, while shared profile and edit views called date methods on it.
- Impact: Authorized profile and edit pages could throw a 500 error before the form rendered.
- Resolution: The shared views now use the model's already-formatted birthday value. Parent profile and assignment-page tests cover the affected screens.

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

## Comment-only grade entry returns HTTP 500 in the live build

- Status: Fixed locally, awaiting deployment
- Area: Gradebook assessment entries
- Observed: Saving a text/comment-only assessment without a `points` field returned HTTP 500 for each learner in the live gradebook.
- Impact: Teachers could not record narrative feedback without assigning a numeric mark.
- Resolution: The controller now treats a missing nullable `points` value as `null`. A regression test covers comment-only grade entries.
- Live follow-up: The live build still needs the fix deployed before this workflow can be re-tested there.

## Automated invoice posting can repeat a batch after a session/redirect failure

- Status: Open
- Area: Finance invoice creation
- Observed: A scripted invoice submission returned an unexpected CSRF/redirect response, but later retries produced repeated posted invoices for the same learners.
- Impact: Retrying after an ambiguous response can create duplicate ledger entries. Posted invoices cannot be deleted by design.
- Workaround: Confirm the invoice list and ledger state before retrying a submission. The duplicate synthetic invoices remain visible for QA and were not removed outside the accounting workflow.
- Follow-up: Add an idempotency key or a clear post/redirect confirmation path for invoice batches.

## Expense creation fails when no programme is selected

- Status: Fixed locally, awaiting deployment
- Area: Finance expenses
- Observed: Creating an expense without an optional `program_id` returned HTTP 500. The production log reported `Undefined array key "program_id"` in `ExpenseController.php`.
- Impact: Office users could not record expenses unless the optional programme field was present in the request.
- Resolution: The controller now treats a missing programme value as `null`. A feature regression test covers recording an expense without a programme.
- Live follow-up: Deploy the fix and re-run the synthetic classroom-materials expense workflow in production.

## Parent assignment page fails with an ambiguous enrollment query

- Status: Fixed locally, awaiting deployment
- Area: Parent–student relationships
- Observed: Opening a parent's assign-students page returned HTTP 500. The production log reported `Column 'user_id' in field list is ambiguous` from the constrained `studentRecord` eager load.
- Impact: Staff could create parent accounts but could not open the page needed to link children.
- Resolution: Enrollment columns are now qualified with the `student_records` table name. A feature regression test covers opening the assignment page.
- Live follow-up: Deploy the fix and link the synthetic Carter students to their parent accounts.
