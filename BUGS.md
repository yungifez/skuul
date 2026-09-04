# Known Bugs

## Exam detail links opened a permanent 404

- Status: Fixed
- Area: Exams and assessment scheduling
- Observed: The exam list generated a detail URL for each exam, but the resource controller's `show` action always aborted with a 404. Staff could create an exam but could not follow its normal detail path to manage exam slots.
- Impact: The assessment workflow stopped after exam creation, leaving exam-slot scheduling unreachable from the user-facing route.
- Reproduction: Create an exam, open `/dashboard/exams/{exam}`, and follow the expected exam detail or slot-management flow.
- Resolution: The exam detail action now renders the existing exam-slot workspace. Regression coverage verifies that an authorized user receives the workspace and can continue to slot management.

## Subject rollover rejected periods with different display labels

- Status: Fixed
- Area: Academic year setup and subject rollover
- Observed: The 2027–28 calendar showed Term 1, Term 2, and Term 3 in the edit form, but its display labels were Autumn, Winter, and Spring. Rolling subjects from 2026–27 reported that the matching reporting period did not exist, even though the period name, type, position, and hierarchy matched.
- Impact: A school could not carry its curriculum into a new year when the new calendar used different presentation labels.
- Reproduction: Open `/dashboard/course-offerings/roll-forward?source_academic_year_id=1&target_academic_year_id=2` with matching period names and different period labels, then review the rollover.
- Resolution: Course-offering rollover now matches reporting periods by stable name, type, position, and parent hierarchy. Display labels are presentation metadata and no longer prevent a valid rollover. Regression coverage includes normal, alternate-label, and missing-period exception flows.

## Production syllabus demo seeder crashed while generating a fake file name

- Status: Fixed
- Area: Demo-school asset creation
- Observed: Running `php artisan db:seed --class=Database\\Seeders\\SyllabusSeeder --force --no-interaction` on Laravel Cloud first failed in `SyllabusFactory.php:22` with `Attempt to read property "name" on null`, then reached `CourseOfferingFactory.php:57` with `Call to undefined function Database\\Factories\\fake()`. No syllabus records were created.
- Impact: The school simulation could not populate syllabus records, so curriculum and learner portal QA could not exercise a populated syllabus workflow.
- Reproduction: Run the existing `SyllabusSeeder` in the production runtime.
- Resolution: The demo seeder now uses existing open or scheduled school offerings and writes deterministic records and PDF placeholders idempotently, so production asset creation does not traverse test-only Faker factories. Cloud command `comm-a2aa40a4-68f3-46bf-ba2f-79f0c9860c37` completed successfully after deployment `171c923e`; live admin and teacher syllabus pages returned 200 and rendered six demo rows, while the learner page returned 200 with the expected scoped empty state.

## Portal roles could read unrelated active notices

- Status: Fixed
- Area: Learner and family notices
- Observed: A learner or guardian with the built-in `read notice` permission could open the workspace notice list, which used the school-wide active-notice query instead of the recipient records used by the portal.
- Impact: A notice aimed at another class, role, or named learner could appear outside its intended audience.
- Reproduction: Publish two active notices, deliver only one to a learner or guardian, then open `/dashboard/notices` and each notice detail route as that portal user.
- Resolution: Portal-role notice lists and detail views now require a published notice with a recipient record for the signed-in account. Staff notice management retains its current school-wide permissions. Regression coverage checks delivered, undelivered, and draft exception paths. Deployed in `a64a94b3`; the live learner and guardian workspaces returned 200 with scoped empty states after release.

## Portal roles could open staff curriculum workspaces

- Status: Fixed
- Area: Learner and family role boundaries
- Observed: On the live site, a student account with the built-in `read syllabus` and `read timetable` permissions could open `/dashboard/syllabi` and `/dashboard/timetables`. The timetable component scoped the learner's rows, but the syllabus list queried the whole school. A guardian account also had the staff-oriented syllabus and timetable navigation available through the seeded read permissions.
- Impact: Learners could be shown curriculum records outside their roster, and guardians could reach staff workspace screens that are not child-scoped.
- Reproduction: Sign in as a student or guardian, open the Syllabi or Timetables workspace directly, and inspect the rendered list and filters. Use a second course offering, section, unpublished syllabus, or draft timetable to verify whether unrelated records are exposed.
- Resolution: The learner timetable route remains a published, own-section view; learners see only published syllabi for active offerings on their roster; guardian access to these staff workspaces is denied and the sidebar and command-palette entries are removed. Regression coverage exercises normal, alternate, and exception paths. Deployed in `a64a94b3`; learner Syllabi and Timetables returned 200 with their scoped empty states, and guardian Syllabi and Timetables returned 403 after release.

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

## Notice links allowed protocol-relative destinations

- Status: Fixed
- Area: Notice editor and rendered notice content
- Observed: The notice sanitizer allowed an `href` beginning with `//`, which can navigate a reader to an external host while looking like a local link.
- Impact: Notice authors could create misleading external navigation in learner and family portals.
- Reproduction: Save a notice containing `<a href="//example.com">Open</a>` and inspect the stored content.
- Resolution: The allow-list now accepts only `http(s)`, `mailto:`, root-relative single-slash, and fragment links. All other link destinations keep the anchor text but lose the `href`; disallowed attributes remain removed. PHPUnit coverage exercises safe links and unsafe protocol-relative and JavaScript links.

## Partial user profiles emitted PHP 8.5 deprecation warnings

- Status: Fixed
- Area: Profile avatars and eager-loaded user summaries
- Observed: Cloud command output reported `trim(): Passing null to parameter #1 ($string) of type string is deprecated` from `User::defaultProfilePhotoUrl()` when a user was loaded with only selected columns such as `id` and `name`.
- Impact: Normal user-summary rendering could pollute production logs and make PHP 8.5 deprecation handling noisy for incomplete or partially selected accounts.
- Reproduction: Build a user model with a missing name or email value, or eager-load `User::query()->select(['id', 'name'])`, then read `profile_photo_url`.
- Resolution: Avatar generation now casts optional profile values to strings and declares its string return type. PHPUnit coverage verifies an incomplete profile produces a usable avatar URL without deprecation warnings.

## Family-request answer validation was not shown in the school inbox

- Status: Fixed
- Area: Family requests
- Observed: An administrator who selected `Answered` without entering a response was redirected back with the request still unchanged, but the inbox did not show the validation message. The page only rendered errors under `status`, while the required response error is keyed as `response`.
- Impact: Staff could think the answer was saved or be left without a reason why the request remained open.
- Reproduction: Open `/dashboard/portal-requests`, select `Answered` for an open request, leave `The answer` empty, and submit.
- Resolution: The inbox now renders the first validation error for any failed status change. PHPUnit coverage verifies the message appears after the failed answer attempt. Live verification confirmed the request remained unchanged before the valid in-review and answered transitions.

## Health-record validation errors were not shown on the edit screen

- Status: Fixed
- Area: Health records
- Observed: Submitting a health record with 5,001 characters in `Anything else` redirected back with the previous value unchanged, but the edit screen showed neither the validation error nor a success message.
- Impact: Staff could not tell why an emergency record was not saved and could repeatedly submit invalid information without feedback.
- Reproduction: Open `/dashboard/health-records/1`, enter more than 5,000 characters in `Anything else`, and save. Confirm the record remains unchanged and inspect the returned page.
- Resolution: The health-record edit screen now renders a page-level destructive alert with the first validation error, and health fields are excluded from flashed input so a large failed value cannot overflow the production cookie session. PHPUnit coverage exercises the valid write, a subsequent invalid update with the cookie session driver, the visible error, and preservation of the last valid value. Live verification confirmed the invalid write was rejected and role boundaries remained intact: administrator 200, parent 403, student 403.
