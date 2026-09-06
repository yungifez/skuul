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
