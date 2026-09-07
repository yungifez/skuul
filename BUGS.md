# Known Bugs

## Portal report-card downloads omitted the academic year

- Status: Fixed
- Area: Learner and family historical documents
- Observed: Staff report-card history showed the academic year, but the learner-facing report-card document showed only `Period: Term 3`.
- Impact: A learner or guardian with records from multiple years could not identify the year of a downloaded report card when period names repeated.
- Reproduction: Sign in as a learner or guardian, open an enrollment's Documents page, download a report card, and inspect the standalone document header.
- Resolution: Portal report-card documents now load and display the academic year beside the period. The existing transcript document already includes `academic year · academic period` for every result. Coverage verifies the year is present in the learner download while portal ownership boundaries remain enforced.

## Historical report cards were ambiguous across academic years

- Status: Fixed
- Area: Report-card history and navigation
- Observed: Report-card filters and rows showed only the period label, so “Term 1” from different academic years looked identical. The records themselves were year-bound, but the user-facing history did not expose that context.
- Impact: Staff could select or review the wrong year when comparing historical report cards, especially after a learner had attended the school for multiple years.
- Reproduction: Publish cards for two years that both contain a period named “Term 1”, then open `/dashboard/report-cards` and inspect the period filter and published-card rows.
- Resolution: Report-card history now provides an academic-year filter, labels period options as `academic year · period`, shows the academic year in each row and detail summary, and rejects mismatched or cross-school year/period selections. Coverage includes normal, duplicate-period alternate, mismatch exception, and cross-school exception flows.

## Historical gradebooks were not discoverable

- Status: Fixed
- Area: Gradebook history and navigation
- Observed: The Gradebooks workspace always queried only the working academic year and period. A staff member could open an older gradebook only if they already had its direct course-offering URL.
- Impact: Historical marks and result history were technically present but difficult to find, and a multi-period historical year could not be reviewed from the gradebook list.
- Reproduction: Open `/dashboard/gradebooks`, then try to select a prior academic year or a different period.
- Resolution: The workspace now provides academic-year and period filters, supports all-period historical views, labels each row with its period and lifecycle state, preserves the working-period default, and rejects cross-school or cross-year period selections. Coverage includes normal, historical, all-period, and invalid-selection flows.

## Closed gradebooks still presented editable controls

- Status: Fixed
- Area: Gradebook usability and academic-period lifecycle
- Observed: A gradebook in a closed reporting period still rendered assessment setup and mark-entry forms. The backend correctly rejected new work, but the screen did not explain the locked state until after a failed submission.
- Impact: Teachers could waste time filling controls that could not be saved, and the page did not clearly separate historical results from current editing work.
- Reproduction: Open a course offering whose reporting period is closed, such as `/dashboard/course-offerings/41/gradebook`, and inspect the assessment setup and learner rows.
- Resolution: Closed gradebooks now show a read-only status banner and summary badges, hide edit controls, keep historical marks/results visible, collapse the workflow guidance, and use a bordered sticky-header grid for long assessment lists. The controller also includes the academic-period status in its eager-load projection so the new state-aware view cannot fail at render time; the summary badges use the card's supported title slot. Screen coverage includes open, closed, and invalid-write paths.

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

## The committed test suite could not run

- Status: Fixed
- Area: Test suite
- Observed: `tests/Feature/CourseOfferingRollForwardTest.php` was committed in Pest style. It called `uses(TestCase::class, RefreshDatabase::class)`, `beforeEach()`, and `it()`. PHPUnit stopped the whole run with "Pest must be run through its own binary".
- Impact: Every test in the project failed to start, not only this file. The committed `composer.json` lists PHPUnit and no Pest, so CI could not run any test.
- Reproduction: Run `vendor/bin/sail php vendor/bin/phpunit`. The run stops before the first test.
- Resolution: The file is now a PHPUnit class. It extends `Tests\TestCase`, uses `FeatureTestTrait` and `RefreshDatabase`, moves the shared setup into `setUp()`, and turns the two global helper functions into private methods. The three roll-forward tests pass. The full suite now runs: 1478 tests.

## Gradebook page two dropped the year and period filter

- Status: Fixed
- Area: Gradebook history and navigation
- Observed: `GradebookController::index()` reads `academic_year_id` and `academic_period_id`, then paginates 25 rows. The paginator built its links without the query string, so page two returned to the working year and period.
- Impact: A school with more than 25 offerings in an older year could not read past the first page of that year. The historical gradebook browsing feature stopped at row 25.
- Reproduction: Open `/dashboard/gradebooks?academic_year_id=<older year>` in a school with 26 or more offerings in that year, then select page 2.
- Resolution: The query now ends with `->withQueryString()`. A new test creates 26 offerings in a historical year and asserts both filter names appear in the rendered pagination links. The test fails without the fix.

## Nested buttons inside links broke keyboard navigation

- Status: Fixed
- Area: Organization workspace
- Observed: `resources/views/pages/organization/show.blade.php` wrapped eight `<april:button>` elements in `<a href>` tags. HTML does not allow a button inside a link.
- Impact: A mouse click reached the link, but keyboard users could not follow it. Enter and Space activated the button, and the button did nothing. Screen readers announced two nested controls for one action.
- Reproduction: Open an organization page, move focus to `Members` with the Tab key, and press Enter.
- Resolution: All eight are now `<april:button-link>`, the component the project already uses for this. A repository-wide search found no other nested pair.

## Form controls without names were unreadable by screen readers

- Status: Fixed
- Area: Accessibility
- Observed: A live audit of the production site found form controls with no accessible name. The course list held 50 unnamed selects, one pair per row. The grading-scale screen held unnamed grade option fields. The financial-period form held three unnamed inputs, two of them bare date fields.
- Impact: A screen reader announced "combo box" or "edit text" with no name. A person could not tell which row a select belonged to, or which date field was the start.
- Reproduction: Open `/dashboard/course-offerings`, `/dashboard/grading-scales`, or `/dashboard/fee-invoices` and read the controls with a screen reader or an accessibility inspector.
- Resolution: The teacher and role selects now name their subject. Grade option fields now carry their option number, including the rows Alpine adds after load. The financial-period form now uses the project's visible `april:label` pattern for `Period name`, `Starts on`, and `Ends on`.

## The school switcher used the same id twice

- Status: Fixed
- Area: Sidebar navigation
- Observed: `april:sidebar` renders its header twice, once for the desktop rail and once for the mobile drawer. The school switcher gave its select `id="sidebar-school-switcher"`, so the id appeared twice on every page of the application.
- Impact: A duplicate id is invalid HTML. The mobile drawer's `<label for>` pointed at the hidden desktop select, so tapping the label focused a control the reader could not see.
- Reproduction: Open any dashboard page with more than one school and run `document.querySelectorAll('#sidebar-school-switcher').length` in the console. It returned 2.
- Resolution: The label now wraps the select instead of pointing at it by id, so no id is needed. The select keeps an `aria-label`.

## Five list screens used the wrong pagination view

- Status: Fixed
- Area: Visual consistency
- Observed: Five screens called `->links()` with no view name, so Laravel rendered its stock Tailwind paginator. That paginator hard-codes `bg-white` and `text-gray-600`.
- Impact: On the dark theme the row count text computed to `oklch(0.373 0.034 259.733)` against a `rgb(12, 18, 15)` background. That contrast is below the WCAG AA minimum, and the control did not match the rest of the site.
- Reproduction: Open `/dashboard/cash-deposits`, `/dashboard/expenses`, `/dashboard/course-offerings`, `/dashboard/reports`, or `/dashboard/academic-cycle-sections` with more than one page of rows.
- Resolution: All five now call `->links('components.pagination-links-view')`, the project's own paginator. Livewire tables keep `components.datatable-pagination-links-view`.

## Four fee-invoice-record routes always answered 404

- Status: Fixed
- Area: Routing
- Observed: `Route::resource('fees/fee-invoices/fee-invoice-records', ...)` registered seven routes. The controller's `index`, `create`, `show`, and `edit` methods each called `abort(404)`. Records are edited from the invoice screen, and no view links to those four routes.
- Impact: Four dead URLs sat in the route table behind authorization checks. Any reader of `route:list` had to open the controller to learn they did nothing.
- Reproduction: Run `vendor/bin/sail artisan route:list --path=fee-invoice-records`.
- Resolution: The resource now declares `->only(['store', 'update', 'destroy'])`, and the four stub methods are deleted.

## A project rule described a bug that does not exist

- Status: Fixed
- Area: Project rules
- Observed: `.ai/rules/queries.md` said an `or` inside a `whereHas` closure escapes the relation's join and matches every row. The rule showed SQL without a group. The installed Laravel version runs the closure through `callScope()`, which groups every condition the closure adds.
- Impact: The rule sent readers to rewrite correct code. Following it, this review first changed a working query in `ListAccountInvitations` and wrote a comment describing a leak that could not happen.
- Reproduction: Run `vendor/bin/sail artisan tinker --execute 'echo App\Models\AccountInvitation::query()->whereHas("user", fn ($u) => $u->where("name","LIKE","%a%")->orWhere("email","LIKE","%b%"))->toSql();'`. The output reads `and (name LIKE ? or email LIKE ?)`.
- Resolution: The rule now states the grouped behaviour, shows the real SQL, and tells readers to confirm with `toSql()` before calling a query a leak. It keeps the warning for an `or` chained outside a closure, where the risk is real. The query in `ListAccountInvitations` is back to its committed form. Two search tests were added, because the invitation search had no coverage at all.

## The profile screen showed two nationality fields

- Status: Fixed
- Area: User profile
- Observed: `update-profile-information-form.blade.php` draws its own `Nationality` input, then embeds the `nationality-and-state-input-fields` component, which drew a second one. Both used `id="nationality"` and `name="nationality"`.
- Impact: The screen asked for nationality twice. The second field was under the `Address` heading and had no `wire:model`, so anything typed into it was discarded without a message. The repeated id also made the page invalid HTML, and both `<label for="nationality">` elements pointed at the first input.
- Reproduction: Open `/user/profile` and run `document.querySelectorAll('[id=nationality]').length` in the console. It returned 2.
- Resolution: The component now takes a `showNationality` flag, and the profile form passes `false`. The two screens that rely on the component to draw the field, `create-user-fields` and `edit-user-fields`, keep the default. Three tests cover the flag and assert the profile screen holds exactly one nationality field. The count test fails without the fix.

## A submitted form stayed dead after the reader went back

- Status: Fixed
- Area: Forms, whole application
- Observed: The global submit handler in `resources/js/app.js` sets `data-submitting="true"` on the form and disables every submit button, to stop a double submit. Nothing ever cleared that state.
- Impact: The browser restores a page from its back-forward cache exactly as it was, and `wire:navigate` restores its own snapshot the same way. The form came back with its buttons disabled and its guard flag set, so the reader could not submit it again. Only a hard reload freed it. This hit every form in the application.
- Reproduction: Submit any form, then press the browser Back button. Run `document.querySelector('form[data-submitting]')` in the console. It returned the form, and its submit button stayed disabled.
- Resolution: `releaseSubmittedForms()` clears the flag, re-enables the buttons, and removes `aria-busy`. It runs on `pageshow` when the page came from the cache, and on `livewire:navigated`. Verified in Chrome against the built bundle: after a submit the button is disabled and the flag is set; after a restore both are cleared.

## Four screens warned about deleting things that are not deleted

- Status: Fixed
- Area: Destructive actions
- Observed: The submit handler asks for confirmation before any `DELETE` form. A form that carries no `data-confirm` gets the default text, "Delete this item? This action cannot be undone." Four screens relied on that default for buttons that do not delete a record.
- Impact: The warning did not describe the action. "Take out of use" only deactivates a facility and keeps its bookings. "Give it up" cancels one booking. "Withdraw" takes a library copy off the shelves. "Take it off" removes a queue place. A reader who trusted the warning expected permanent deletion in all four cases.
- Reproduction: Open `/dashboard/facilities`, `/dashboard/library`, `/dashboard/library/queue`, or `/dashboard/grading-scales` and press any destructive button.
- Resolution: Each form now carries `data-confirm` text that names its own action. Three tests assert the exact wording on the four screens. All four fail without the fix, because no `data-confirm` existed on any of these pages before.

## Every dashboard page held two main landmarks

- Status: Fixed
- Area: Page structure, whole application
- Observed: `april:sidebar-inset` renders a `<main>` element. `layouts/app.blade.php` then rendered its own `<main id="main">` inside it.
- Impact: HTML does not allow a `<main>` inside another `<main>`. A screen reader announced two main regions on every dashboard page, so "jump to main content" landed the reader in the wrong one.
- Reproduction: Open any dashboard page and run `document.querySelectorAll('main').length` in the console. It returned 2, and the second was a descendant of the first.
- Resolution: The inner element is now a `<div id="main" tabindex="-1">`. It keeps the same classes, so nothing moves. `tabindex` lets it accept focus, which a `<div>` cannot do on its own and which the old `<main>` could not do either.

## The skip link stayed invisible while it held focus

- Status: Fixed
- Area: Keyboard navigation
- Observed: The "Skip to content" link carried only `sr-only`. That class clips the link to one pixel and never releases it.
- Impact: The link is the first stop for a keyboard reader on a page with over a hundred sidebar links. Focus moved to a control nobody could see, so a sighted keyboard reader lost track of the focus position and could not tell the link was there.
- Reproduction: Open any dashboard page, press Tab once, then look at the top left corner. Nothing appeared.
- Resolution: The link now carries `focus:not-sr-only` with position, padding, background, and a focus ring. Verified in Chrome: while blurred the link measures 1x1 and is clipped; once focused it measures 125x36 at the top left corner, with `clip-path: none`. Three tests in `AppLayoutTest` cover the single landmark, the focusable target, and the focus style. All three fail without the fix.

## Link buttons were painted in a colour nobody could read

- Status: Fixed
- Area: Colour contrast, whole application
- Observed: April UI paints its `link` button variant with `text-primary`, and the rich text editor paints its links the same way. `--primary` is a surface colour in this theme, not a text colour: a light tan on the light background, a dark brown on the dark one.
- Impact: The text landed at 1.59:1 on the light theme and 1.73:1 on the dark one. WCAG AA asks for 4.5:1. The colour is close enough to the page background that the link is hard to find at all. This hit every "link" button in the application, including several primary actions in table rows.
- Reproduction: Open any page with a link button and measure the computed colour against the page background. Light theme: `rgb(214, 187, 164)` on `rgb(235, 241, 234)`. Dark theme: `rgb(84, 54, 28)` on `rgb(12, 18, 15)`.
- Resolution: `resources/css/app.css` repaints these links with `--primary-foreground`, the readable half of the same colour pair. It holds the same brown hue, so the links keep their look. They now measure 12.32:1 on the light background and 8.11:1 on the dark one. The rule sits outside every `@layer`, because a layered rule loses to a Tailwind utility whatever its specificity. Three tests cover it, including one that fails the day April UI changes the variant itself.

## Calendar weekday headers were too faint to read

- Status: Fixed
- Area: Colour contrast, calendar screens
- Observed: The weekday row on the calendar grid, and the header row of the calendar template table, paired `text-muted-foreground` with a `bg-muted/50` surface.
- Impact: On the light theme the pair measures 4.39:1. WCAG AA asks for 4.5:1 on normal text. The gap is small enough to look fine in a screenshot and still fail a reader with low vision. The affected text is the column label, so a reader who cannot make it out cannot tell which column is which day.
- Reproduction: Open `/dashboard/calendar-events` on the light theme and measure "Mon" against the strip behind it. Foreground `rgb(85,99,90)`, background `rgb(210,217,206)`.
- Resolution: The three header rows now use `text-foreground`. A test walks every Blade view and fails if any line pairs `bg-muted/50` with `text-muted-foreground`, so the combination cannot come back anywhere. The test fails without the fix.

## Destructive text was unreadable on the dark theme

- Status: Fixed
- Area: Colour contrast, whole application
- Observed: `text-destructive` reads `--destructive`. On the dark theme that red measures 4.3:1 on a card, 3.73:1 on a muted surface, and 3.61:1 on an accent surface, against the 4.5:1 minimum.
- Impact: Destructive text names the actions a reader most needs to read before pressing, such as "Delete scale". The light theme passes at 4.51:1, so the fault only shows for readers on the dark theme.
- Reproduction: Open `/dashboard/grading-scales` on the dark theme and measure "Delete scale". Foreground `rgb(225,70,70)`, background `rgb(21,27,22)`.
- Resolution: `--destructive` doubles as the background of destructive buttons, so lightening it would repaint those buttons. The application adds `--destructive-text` instead. The light theme keeps the shared token. The dark theme raises the lightness to 68%, which clears 4.5:1 on every dark surface the application uses: 6.46:1 on the page background, 5.97:1 on a card, 5.17:1 on a muted surface, and 5.00:1 on an accent surface. Five tests cover it, including one that fails the day April UI gives destructive text its own readable colour.

## The sidebar was not a navigation landmark

- Status: Fixed
- Area: Screen reader navigation, whole application
- Observed: The sidebar holds over a hundred links, grouped under headings. It rendered as plain `<div>` elements, so it was not a landmark.
- Impact: A screen reader reader moves between landmarks to skip past a menu. With no landmark the sidebar was not in the list, so the only way past it was to read through every link on every page.
- Reproduction: Open any dashboard page and run `document.querySelectorAll('nav, [role=navigation]')` in the console. Only the pagination trail came back.
- Resolution: The sidebar content slot now carries `role="navigation"` and `aria-label="Main"`. april merges slot attributes onto the wrapper it already draws, so no element was added and nothing moved. The label separates it from the pagination trail. april draws the sidebar twice, once for the desktop layout and once for the mobile sheet, and only one is visible at a time, so both carry the landmark. A test asserts both are present and fails without the fix.

## Every screen announced two top-level headings

- Status: Fixed
- Area: Page structure, whole application
- Observed: The top bar marked the product name as an `<h1>`. `layouts/app.blade.php` draws a second `<h1>` for the page heading.
- Impact: A screen reader reader who lists the headings on a page found two at the top level, and the first was the word "Skuul" on all 48 screens. The name gave no clue which page was open, and the real page heading came second.
- Reproduction: Open any dashboard page and run `document.querySelectorAll('h1').length` in the console. It returned 2 on every page.
- Resolution: The product name is now a `<span>`. It keeps its size and weight, so nothing moves. The link around it already carries `aria-label="Home"`, so no label was lost. A test asserts one `<h1>` per screen and fails without the fix.

## Two controls were too small to press on a phone

- Status: Fixed
- Area: Touch target size, whole application and the calendar
- Observed: A sweep of all 48 dashboard screens at a 390px viewport measured every control. The sidebar toggle rendered at 18x28px on 44 screens. The add-day links on the calendar grid rendered at 12x12px, 35 of them on one screen.
- Impact: WCAG 2.2 asks for 24x24px. The sidebar toggle is the only way to reach the menu on a phone, so a reader who misses it cannot navigate at all. The toggle asks for `size-7`, but it sits on a flex line beside a `min-w-0` block, so a narrow screen squeezed it below its own width.
- Reproduction: Open any dashboard page at 390px wide and measure the toggle. It returned 18x28. Open `/dashboard/calendar-events` and measure a plus icon. It returned 12x12.
- Resolution: The toggle now carries `shrink-0`, so the flex line cannot squeeze it. The calendar plus icon is now wrapped in a padded 24x24 link that also carries an `aria-label` naming the day. Neither change moves anything on a wide screen. Two tests cover them, and both fail without the fix.

## Phone fields opened a letter keyboard on a phone

- Status: Fixed
- Area: Mobile data entry, people forms and health records
- Observed: A sweep of all 107 dashboard route shapes read every form control. The school form and the organization form already set `type="tel"` on their phone field. The create-person form, the edit-person form, the profile form, and the emergency contact number on a health record left theirs as plain text.
- Impact: A phone shows the letter keyboard for a text field. The person must switch to the number layer for every digit. The emergency contact number is the worst case: it is typed by a nurse who is often standing next to the child.
- Reproduction: Open `/dashboard/students/create` on a phone and press the phone field. The letter keyboard opens.
- Resolution: The four remaining phone fields now carry `type="tel"`. The health record view takes an optional `type` per field, so the other three fields on that card stay plain text. The profile phone field also carries `autocomplete="tel"`, because it is the reader's own number. The other people forms record somebody else's number, so they are deliberately left without autocomplete. Two tests cover it. One walks every Blade view and fails if any phone field lacks `type="tel"`, so the fault cannot come back. Both fail without the fix.

## Every row on a list said the same thing to a screen reader

- Status: Fixed
- Area: Screen reader navigation, twenty list screens
- Observed: A sweep of all 107 dashboard route shapes grouped the links on each screen by the name a screen reader reads. Fifteen screens held a row action whose name repeated on every row and pointed somewhere different each time. The worst were 25 links called "Open gradebook", 25 called "Edit roster", 20 called "Open", and 20 called "View".
- Impact: A screen reader can list every link on a page. That list read "Open, Open, Open" twenty times over, with nothing to say which record each one opened. The only way to tell them apart was to leave the list and read the table row by row.
- Reproduction: Open `/dashboard/health-records` and run `[...document.querySelectorAll('main a')].map(a => a.textContent.trim())`. Twenty entries came back reading "Open".
- Resolution: Twenty views now give the row action an `aria-label` naming the row: "Open the health record for Ada Bell", "Open the gradebook for Mathematics, Year 7". The visible text is unchanged, so nothing moves and the button stays short. Two tests cover it. One walks every Blade view and fails if a link inside a loop carries a generic name with no `aria-label`, so the fault cannot come back on a new screen. Both fail without the fix.

## The keyboard focus ring was almost invisible

- Status: Fixed
- Area: Keyboard navigation, whole application
- Observed: April UI binds `--ring` to `--primary`. That token is a surface colour, so the ring the browser draws is nearly the colour of the page behind it. On the light theme it measured 1.59:1 on the page background, 1.71:1 on a card, 1.39:1 on a muted surface and 1.33:1 on an accent. On the dark theme it measured 1.73:1, 1.62:1 and 1.39:1. WCAG 2.2 asks for 3:1 on a focus indicator.
- Impact: A reader who moves by keyboard could not see where the focus had gone. Every control on every screen was affected, including the login form and each row action on a list.
- Reproduction: Open any page, press Tab, and read the focused control. `getComputedStyle` returned a ring of `rgb(84,54,28)` on a `rgb(12,18,15)` page. A screenshot showed no visible ring at all.
- Resolution: `resources/css/app.css` binds `--ring` to `--primary-foreground` in both themes. That is the readable half of the same colour pair and holds the same brown hue, so the ring keeps its look. It now measures 12.32:1 on the light background and 8.11:1 on the dark one, and the worst surface in either theme is 6.37:1. Three tests cover it, including one that fails the day April UI gives the ring its own readable colour.

## The breadcrumb trail named the same page three different ways

- Status: Fixed
- Area: Navigation, twenty-eight screens
- Observed: The breadcrumb trail is a list of page names, but the older screens wrote those names by hand and never agreed. The administrator list was called "Administrators" on its own screen, "Administrator" on the create screen, and "admins" on the edit screen. Twenty-three crumbs started with a small letter, so a trail read "Dashboard > students > Edit Ada Bell". Twelve more crumbs said only "Create" or "Edit" and never named the page.
- Impact: A reader uses the trail to learn where they are and to go back one step. A crumb that reads "students" on one screen and "Students" on the next looks like two different places. A crumb that reads only "Create" says nothing about what is being created, and it is the one crumb the reader lands on. Screen readers read the trail aloud, so the mismatch is heard as well as seen.
- Reproduction: Open `/dashboard/students`, then open a student and press Edit. The trail changed from "Dashboard > Students" to "Dashboard > students". Open `/dashboard/fees/create`. The trail ended in "Create".
- Resolution: Every crumb now carries the name of the page it points at, taken from that page's own heading. A crumb that names an action now names the record class with it: "Create student", "Create fee invoice". Four page headings were reworded to match, including "Create Fees Invoice", which was not grammatical. A bare "Edit" is still allowed where the crumb before it names the record, because "Classes > Kindergarten 1 > Edit" reads well. Two tests cover it. They walk every Blade view, so a new screen cannot bring the fault back. Both fail without the fix.

## Five checkboxes were painted by the browser, not by the app

- Status: Fixed
- Area: Visual consistency and target size, four screens
- Observed: Five checkboxes carry no class of their own, so Chrome paints them itself. On the grading scale screen the box beside "Available for new assessments" rendered bright blue at 13x13, on a page whose every other accent is warm brown. The same screens also hold hand-painted checkboxes of the same meaning, 16x16 and brown, so one screen showed two kinds of the same control.
- Impact: The blue is the operating system accent colour, so it changes from reader to reader and belongs to no theme. On the dark theme it reads as a control from another program. At 13px it was also the smallest target in the application, three pixels under every other checkbox.
- Reproduction: Open `/dashboard/grading-scales` and read the checkbox. `getBoundingClientRect` returned 13x13 and `accentColor` returned `auto`, which Chrome resolves to its own blue.
- Resolution: `resources/css/app.css` sets `accent-color` and a 1rem size on every native checkbox and radio. The hand-painted ones set `appearance: none`, and a browser ignores `accent-color` once it does, so they are untouched. Every sized checkbox in the app already measures 1rem, so nothing else moves. Three tests cover it, including one that fails if a new view asks for a size the repaint would override.

## Every Livewire form stopped working after its first submit

- Status: Fixed
- Area: Forms, nine forms across seven screens
- Observed: The bundle guards against a double submit. On submit it flags the form and disables every submit button inside it. Two things undo that flag: the browser restoring a cached page, and a `wire:navigate` visit. A `wire:submit` form does neither. Livewire cancels the native submit and answers over AJAX, so the page never moves and neither event ever fires. The button stayed disabled and the flag stayed set.
- Impact: The reader pressed the button once and it went dead. Nothing said why, and the only way back was a full page reload. This hit the promotion and graduation screens, the timetable time slot form, the academic calendar form, the organization member form, the three status forms on a student profile, and every profile screen that uses the shared form section.
- Reproduction: Open `/dashboard/students/promote` and submit the "Load students" form. The button came back with `disabled` still set and `aria-busy="true"` still on it. Pressing it again did nothing. The same state was reproduced in the live page by dispatching a cancelable submit on a copy of the form: `{"disabled":true,"flag":"true"}` before and after a Livewire round trip.
- Resolution: `resources/js/app.js` now registers a Livewire `commit` hook and releases the form when the request comes back, on both the response and the failure path. The release only touches forms carrying a `wire:submit` attribute, so a plain form still stays disabled while it navigates and cannot be sent twice. The fix was proven in the live page: the same stuck form read `{"disabled":false,"flag":null}` once the hook was in place. Five tests cover it, three of which fail without the fix.

## A refused field said nothing on the control itself

- Status: Fixed
- Area: Forms, 46 screens
- Observed: The application refuses a field by printing a red line under it. Nothing joined the two. No view in the application used `aria-invalid`, and only two used `aria-describedby`, against 166 error blocks across 48 views.
- Impact: A screen reader read the control as if nothing were wrong. It gave the label, then the value, then moved on. The message sat on the page as loose text with nothing to say which field it belonged to, so a reader who could not see the layout had no way to tell. On a long form the reader was told the form was refused and then had to hunt for the field.
- Reproduction: Open any create screen, submit it empty, and read the refused control. `getAttribute('aria-invalid')` returned null and `getAttribute('aria-describedby')` returned null, while the message was on the page.
- Resolution: A refused control now carries `aria-invalid="true"` and points at its own message with `aria-describedby`. The message carries the matching id. Three helpers in `app/helpers.php` work that id out, so both sides always agree. 162 error blocks became `<x-field-error name="..." />`, which renders the same markup and adds the id. 61 native controls carry `{{ field_error_bindings('...') }}`. `april:input`, `april:native-select` and `april:textarea` read their own `name` or `wire:model` binding, so 72 more controls needed no view change at all; that took three small overrides under `resources/views/vendor/april/components/`, because a Blade directive inside an `<april:*>` tag breaks April's tag precompiler. Three tests cover it and two fail without the fix. `.ai/rules/views.md` records the convention.
- Not covered: 13 controls that use `april:select`, `april:combobox` or `april:editor`. See the next entry.

## The April select and combobox were not announced as selects

- Status: Fixed
- Area: Forms, 12 controls
- Observed: `april:select` and `april:combobox` build their own menu out of a plain `<button>` and a `<div role="listbox">`. The trigger carried no `role="combobox"`, so nothing said the button was a select. The `april:select` list was never tied back to its trigger and never said whether it took more than one answer. An earlier reading of this recorded `aria-expanded`, `aria-controls` and `aria-haspopup` as missing as well. That was wrong: April binds all three from Alpine at runtime, so they were never absent from the live control, only from the view file that was read.
- Impact: A screen reader announced a button. It did not say a list of options sat behind it, and on a multiple-choice select it did not say more than one answer was allowed.
- Reproduction: Open a screen with an `april:select` and read the trigger. It was a `<button type="button">` with no role of its own.
- Resolution: Fixed in April UI 1.2.6. The select trigger and the combobox search field carry `role="combobox"`. The select list points back at its trigger with `aria-labelledby` and reports `aria-multiselectable`, and the trigger carries the id that link needs. A rendering test and a browser test cover it in the package.
- Still open: these 12 controls carry no `aria-invalid` or `aria-describedby` when a field is refused, unlike the controls in the entry above. The composite control would have to read the error state itself.

## Every card and alert title skipped a heading level

- Status: Fixed
- Area: Page structure, 45 dashboard screens
- Observed: A sweep of 45 live dashboard pages found 33 skipped heading levels: 32 read `h1` then `h3`, and one read `h1` then `h5`. The page heading is the only `h1`. April UI fixed a card title at `h3`, an alert title at `h5`, a dialog and sheet title at `h4`, and a dropdown menu label at `h6`. Two application views added to it: the command palette labelled its dialog with an `h2` that sat before the page `h1`, and a toast notification titled itself with an `h5`.
- Impact: A screen reader reader moves through a page by heading. When the level jumps from 1 to 3 that reader cannot tell whether a section was missed or the page simply left a number out, so they stop trusting the outline and read the whole page instead. WCAG technique G141 asks for heading levels that step down one at a time.
- Reproduction: Open any dashboard screen and read the headings in order. `/dashboard/cohorts` gave `h2` (command palette), `h1` (page), `h3` (card), `h3` (card), `h5` (toast). `/dashboard/calendar-events` gave `h1` then `h5` for "1 event is still a draft".
- Resolution: Fixed in April UI 1.2.6. Card, alert, dialog-header and sheet-header now render their title as an `h2`, one level under the page heading, and take a `level` prop for a title that belongs inside a section. A dropdown menu label names a group of items inside `role="menu"`, so it is now a `div` and stays out of the heading order. On the application side the command palette labels its dialog with a `<p>`, because `aria-labelledby` takes any element, and the toast titles itself with a `<p>`, because `role="alert"` reads the whole message out and a message that disappears does not belong in the heading list. Three tests walk real screens and fail on any skipped level.

## Adding a fee to an invoice crashed the screen

- Status: Fixed
- Area: Finance, the create fee invoice screen
- Observed: The waiver box on a fee row carried `:max="amount"`. Blade reads a leading colon as a PHP expression, not as an Alpine binding, so the view compiled to a constant named `amount`. The row threw "Undefined constant \"amount\"" as soon as it rendered. A stray bare `x-bind` attribute beside it suggests somebody had already tried to work around this.
- Impact: The screen worked until the first fee was added, then the whole Livewire component threw and the reader saw an error page. No invoice could be raised from this screen at all. The waiver cap the binding was meant to apply never worked either.
- Reproduction: Open the create fee invoice screen and press "Add Fee(s)". Rendering the component with one added fee throws a `ViewException`.
- Resolution: The binding is written in Alpine's long form, `x-bind:max="amount"`, which Blade passes through untouched. `tests/Feature/ControlNameTest.php` renders the component with a fee added; it throws without the fix.

## A form control in a table row did not say what it was for

- Status: Fixed
- Area: Forms, three screens
- Observed: Seven controls sat in table cells with no name of their own. The house roll gave each boarder a status select and two text boxes, the create fee invoice screen gave each fee an amount, a waiver and a fine box, and the gradebook reject box carried only a placeholder. A column heading names the column. It is not the accessible name of a control inside the column.
- Impact: A screen reader read a row as an unlabelled select and two unlabelled boxes, repeated once per boarder or per fee. Nothing said which row was being filled in. WCAG 2.1 SC 1.3.1 and 4.1.2 both ask for a name on the control itself.
- Reproduction: Open a house roll with one boarder and read the status select. It had no `aria-label`, no `aria-labelledby`, and no label pointing at an id.
- Resolution: Each control names its row and its column, for example `aria-label="Status for Ada Bello"`. This follows the convention the gradebook mark boxes already used. `tests/Feature/ControlNameTest.php` renders all three screens and fails on any control in a table cell that nothing names.

## Removing a fee moved typed amounts onto the wrong fee

- Status: Fixed
- Area: Finance, the create fee invoice screen
- Observed: The added-fee rows and the added-student rows carried no `wire:key`. Livewire matches rows by position when no key says otherwise. A typed value lives on the DOM node, not in the markup Livewire sends, so removing a row from the middle left the typed amounts where they were while the fee ids under them shifted up by one.
- Impact: The reader removed one fee and the amounts, waivers and fines they had already typed silently moved onto the fees below. The `name` attributes moved with the markup, so the wrong figures were posted under the wrong fee ids. The running total in the last column showed the same wrong numbers, so nothing looked out of place.
- Reproduction: Add three fees, type an amount into each, then remove the first. The second row kept the first row's amount.
- Resolution: Both loops key their rows by record id, so Livewire moves the right row. `resources/views/livewire/assign-students-to-parent.blade.php` rebuilds its rows on a Livewire action too and is keyed the same way.

## Every destructive button asked the same vague question

- Status: Fixed
- Area: Forms, 15 screens
- Observed: `resources/js/app.js` asks the reader to confirm any form carrying the DELETE method. Fifteen hand-written forms gave it no message, so all fifteen read "Delete this item? This action cannot be undone." Three of them do not delete anything: returning a campus to the default calendar, ending a boarding placement and revoking an invitation all use the DELETE method for a change that is not a deletion. A sixteenth form, the fee record on the edit invoice screen, already asks in its own dialog, so the reader was asked twice in a row.
- Impact: The question named nothing, so a reader with several houses, budgets or domains on screen could not tell which one they were about to lose. Worse, three actions that are reversible announced themselves as permanent deletions, and one asked twice, which teaches a reader to click through the question without reading it.
- Reproduction: Open `/dashboard/organizations/{id}/domains` and press "Give it up". The browser asked "Delete this item? This action cannot be undone." with no mention of the domain.
- Resolution: Each of the fifteen forms carries a `data-confirm` that names what it undoes, for example "Give up boarding.example.test? Nobody reaches the school at this address afterwards." The handler now honours `data-confirm="false"`, so the fee record form, which has its own dialog, asks once. That dialog names the fee instead of saying "this resource". `tests/Unit/DestructiveFormConfirmationTest.php` fails on any new DELETE form without a message of its own.

## Two boxes on the edit invoice screen shared one id

- Status: Fixed
- Area: Finance, the edit fee invoice screen
- Observed: Each fee row rendered a waiver box and a fine box, both with `id="name-{id}"`. Both labels pointed at the same id. The rows also carried no `wire:key`.
- Impact: Pressing the "Fine" label put the cursor in the waiver box, and a screen reader read the fine box as "Waiver". A duplicate id also breaks any script that looks a box up by id.
- Reproduction: Open the edit screen for an invoice with one fee and read the two ids. Both were `name-` followed by the same record id.
- Resolution: The boxes take `waiver-{id}` and `fine-{id}`. The "fine" label reads "Fine", like every other label on the row, and each row carries a `wire:key`.

## Removing a paid fee deleted the record of where the money went

- Status: Fixed
- Area: Finance, fee invoice records
- Observed: `FeeInvoiceRecordService::deleteFeeInvoiceRecord()` refused to remove a line from a posted invoice, but allowed one that already had money against it. `payment_allocations.fee_invoice_record_id` is declared `cascadeOnDelete`, so the database removed the allocations with the line. The sibling method `updateFeeInvoiceRecord()` guards this exact case, which shows the author had it in mind for a change but not for a removal.
- Impact: The `student_payments` row kept its full amount while the allocations that said what it settled were gone. The invoice then reported less paid than the family had handed over, so the office asked for money it already held. Nothing in the audit trail said the allocations had ever existed.
- Reproduction: Raise an invoice, take a payment against it, then press "Delete" on the paid fee row. The line and its allocations both went, and the invoice showed the fee as unpaid.
- Resolution: The service refuses to remove a line whose `paid` is positive and says to raise the waiver instead. The edit screen no longer offers the button on such a row; it explains what has been paid. Its dialog now names what the family stops owing instead of promising that payments stay on record, which was never true. `tests/Feature/FeeInvoiceRecordTest.php` takes a payment and then fails if the line can still be removed.

## Three tables showed a heading over blank space

- Status: Fixed
- Area: Finance and the family portal
- Observed: The fee table on the invoice screen and both tables in the printed portal document looped without an empty branch. An invoice can reach zero fees, because every line can be taken off. A report card can be published before any subject is marked, and the view already wrote `payload['results'] ?? []`, which says the author knew the list could be empty.
- Impact: The reader saw column headings with nothing under them and no way to tell an empty record from a screen that had failed to load. On a printed report card a family got a blank grid with no explanation.
- Reproduction: Open an invoice with no fee lines, or download a report card whose payload holds no results.
- Resolution: All three loops use `@forelse` and say what is missing: "No fees are on this invoice yet.", "No subject was marked in this period." and "No subject has been marked yet." `tests/Feature/FeeInvoiceTest.php` and `tests/Feature/PortalTest.php` cover them.

## Deleting a taught subject broke every course offering screen

- Status: Fixed
- Area: Academics, the subject catalog
- Observed: `SubjectService::deleteSubject()` removed a subject with no check on what taught it. `Subject` uses soft deletes, so the row stayed but the `subject` relation on a course offering read null. Every screen that names a subject reads it straight off that relation, for example `{{ $courseOffering->subject->name }}` on the course offerings index and at the top of every gradebook.
- Impact: One press of "Delete subject" took the course offerings index, the gradebook list and every gradebook screen to a 500 error. Nothing on the subject screen said the subject was in use, and nothing pointed at the subject as the cause once the screens had gone.
- Reproduction: Create a subject, give it a course offering, delete the subject, then open `/dashboard/course-offerings`. The page returned 500 with "Attempt to read property name on null".
- Resolution: The service refuses to delete a subject that has a course offering and says to close its offerings first. The catalog hides the delete action on any subject whose offering count is above zero, so the screen no longer offers an action the server will refuse. `tests/Feature/SubjectTest.php` deletes a taught subject, then opens the course offerings index.

## A row action was offered where the server would refuse it

- Status: Fixed
- Area: Tables, `x-table-actions`
- Observed: `x-table-actions` rendered every action on every row. A row where the action could not work still showed the button, and the reader only learned that after pressing it.
- Impact: A reader who pressed such a button was bounced back with a message and no change. Repeated often enough, that teaches a reader to distrust the row actions.
- Reproduction: Open the subject catalog with one taught subject. The delete action showed on it, and pressing it did nothing but raise an error.
- Resolution: An action may carry a `when` key holding an Alpine expression over `row`, for example `row.course_offerings_count === 0`. The control is hidden on rows the expression rejects. The expression is worked out in a `@php` block, because a Blade directive inside an april tag breaks its precompiler.

## Deleting a fee or its category broke the screens that named it

- Status: Fixed
- Area: Finance, the fee catalog
- Observed: `FeeService::deleteFee()` and `FeeCategoryService::deleteFeeCategory()` both deleted with no check on what pointed at them. Both models soft delete, so the row stayed while every `belongsTo` back to it read null. `show-fee-invoice.blade.php` and `edit-fee-invoice-form.blade.php` read `$record->fee->name`, `ListFeesTable` reads `$fee->feeCategory->name`, and `FeePolicy` reads `$fee->feeCategory->school_id` on every check.
- Impact: Deleting a fee that was on an invoice took both invoice screens to a 500 error, so the invoice could no longer be read or corrected. Deleting a category took the whole fees index down, and the policy threw on any single fee, so even opening one fee failed. This is the same fault as the taught subject, in the two places above it.
- Reproduction: Raise an invoice carrying one fee, delete that fee from the catalog, then open the invoice. The page returned 500.
- Resolution: A fee that is on an invoice and a category that holds fees are both refused, with a message saying what to clear first. Both tables hide the delete action on rows that are in use, using the `when` key added to `x-table-actions`. `tests/Feature/FeeTest.php` and `tests/Feature/FeeCategoryTest.php` delete a record that is in use, then open the screen that reads it.

## Removing a student took down the fee invoices index

- Status: Fixed
- Area: Finance, invoices and receipts
- Observed: `StudentService::deleteStudent()` soft deletes the person. `FeeInvoice::user()` and `StudentRecord::user()` were plain `belongsTo` relations, so both read null once the person was removed. `ListFeeInvoicesTable` reads `$invoice->user->name` on every row, and `show-fee-invoice.blade.php` and `edit-fee-invoice-form.blade.php` both read it at the top of the screen.
- Impact: Removing one student took the fee invoices index to a 500 error for the whole school, so no invoice could be read or paid. Every screen that names a learner through their enrollment fell back to the admission number, so a report card, a boarding roll and a payment receipt all stopped naming the person they were about.
- Reproduction: Raise an invoice for a student, remove the student, then open `/dashboard/fees/fee-invoices`. The page returned 500 with "Attempt to read property name on null".
- Resolution: Both relations resolve a removed person with `withTrashed()`. A refusal is wrong here: a school may remove a student, and the invoices raised for them are real financial history that has to keep naming them. `tests/Feature/FeeInvoiceTest.php` removes an invoiced student, then opens the invoice, its edit screen and the index.

## A deleted invoice broke the policy on every line it carried

- Status: Fixed
- Area: Finance, invoice lines
- Observed: `FeeInvoice` soft deletes, but `FeeInvoiceRecord` does not, so a line outlives the invoice it sat on. `FeeInvoiceRecordPolicy` reads `$feeInvoiceRecord->feeInvoice->school_id` on all three of its checks, and that relation read null once the invoice was gone.
- Impact: Any request touching a line whose invoice had been deleted returned a 500 from the policy, before the request reached a controller. A policy is the wrong place to fail: it should decide yes or no, not throw.
- Reproduction: Raise an invoice with one fee, delete the invoice, then send a DELETE to that line's route. The policy threw "Attempt to read property school_id on null".
- Resolution: `FeeInvoiceRecord::feeInvoice()` resolves a deleted invoice with `withTrashed()`, so the policy reads the school and answers normally. `tests/Feature/FeeInvoiceRecordTest.php` covers it.

## Money on the finance screens did not name its currency

- Status: Fixed
- Area: Finance and the family portal
- Observed: A model column cast to `Money` formats itself and prints "NGN 250,000.00". A figure that arrives as a plain number, such as a sum or a `decimal` column, had nothing to format it, so ten screens rendered money with `number_format($amount, 2)` and no currency at all.
- Impact: The fee invoices index showed "1,897,000.00" in its summary cards directly above a table showing "NGN 147,000.00", so the same page disagreed with itself about the same money. The budget register, the expense register, the cash deposit list and both ledger account balances did the same. Worst of all, the family portal told a parent they still owed "250,000.00", with nothing saying in what.
- Reproduction: Open `/dashboard/fees/fee-invoices`. The four cards carried bare numbers while the table under them carried the currency.
- Resolution: A new `money_text()` helper in `app/helpers.php` formats a plain amount through `Brick\Money`, the same way the `Money` cast does, so both routes read identically. All ten renders use it. It takes a major amount, rounds half up so a float sum cannot throw, and treats null as zero. `tests/Feature/FeeInvoiceTest.php` and `tests/Feature/PortalInvoiceScreenTest.php` both fail if a bare figure comes back.

## A section select named "A" without saying which class

- Status: Fixed
- Area: Calendar, timetables, course offerings
- Observed: A section is named "A" or "B" inside its class, so the name means nothing on its own. Five screens rendered `$section->name` with no class beside it: the calendar event audience checkboxes and the audience column on the calendar index, the class select on the timetable screen, the class select on the course offering edit screen, and the clash message from `FacilityAvailability`.
- Impact: Choosing who a calendar event is for meant picking between three boxes all labelled "A". The reader could not tell JSS 1 A from JSS 2 A, so an event could be sent to the wrong class with nothing on the screen to catch it. The room clash message named a section the reader could not place.
- Reproduction: Open `/dashboard/calendar-events/2`. The audience list showed "A", "B", "A" with no class.
- Resolution: `AcademicCycleSection::qualifiedName()` writes the class and the section together, as "JSS 1 · A", and `displayName()` prefers the school's own label where it set one. The five ambiguous renders use it. The three queries behind them widened their column select and eager load the level, so the accessor cannot throw or lazy load in a list. Sixteen other renders keep the bare name, because a class column already sits beside them. `tests/Feature/CalendarEventScreenTest.php` covers both calendar screens.
