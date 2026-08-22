# Skuul Modernization Plan

Status: Active stabilization

Skuul is a multi-school management system. This plan separates the product into business features before we change the code.

Do not start a large rewrite from this document alone. Confirm each business decision first.

## Goals

- Make business rules clear and flexible.
- Protect data between schools.
- Keep academic history correct.
- Support common school workflows without hidden assumptions.
- Replace large changes with small, testable feature changes.
- Keep the current application usable during the work.

## Working rules

1. Describe the user workflow before changing its database model.
2. Keep historical records. Do not overwrite history when a new state is needed.
3. Enforce school ownership in one clear place.
4. Keep authorization separate from display logic.
5. Validate input at the boundary and enforce important rules in the domain layer.
6. Use database constraints for values that must be unique.
7. Add a regression test for every security or data-integrity fix.
8. Upgrade one vertical feature at a time.
9. Keep the current Blade and Livewire screens until a replacement screen works.
10. Do not preserve obsolete behavior, schema, screens, APIs, or workflows for
    backward compatibility. This rewrite is intentionally breaking when a
    complete replacement produces the correct product.
11. Do not add adapters, bridge columns, dual writes, fallback reads, or
    compatibility modes for an obsolete design. Keep data only when a current
    product, legal, or retention requirement needs it.
12. Use `../livesound-planner` as the reference for UI architecture. Prefer
    useful index pages, inline create and edit flows, clear context, and fewer
    navigation steps. Reuse its interaction patterns; do not copy its domain
    model or visual identity.

## Release-readiness gates

These gates apply to every modernization release.

1. Do not remove or transform personal data until the retention decision, data
   migration, verification query, backup, and restore procedure are approved.
2. Treat a deployed destructive migration as forward-only. Do not edit it.
   Record the data loss, recover from a verified backup when possible, and
   ship a separate remediation release.
3. Test the active April UI development branch in CI before every deployment.
   Resolve its Tailwind and Livewire contract in this application.
4. Migrate one complete user workflow at a time. Each workflow needs server
   tests, a rendered-page smoke test, and Chrome checks on desktop and mobile.
5. A Chrome check must confirm that the workflow has no console errors, no
   failed page assets, keyboard access to each control, and no critical
   accessibility audit failures.
6. Decide the organization and campus ownership model before adding more
   school-owned feature tables. A school-only implementation is not a safe
   substitute for an organization-and-campus model.
7. Release only after CI runs the current test suite, Pint, Larastan, the
   dependency audit, and the frontend build from a clean environment.

Known data incident:

- The migration `2026_08_21_300000_remove_religion_and_blood_group_from_users_table`
  has run in the shared environment. It removed religion values and can remove
  blood-group values that do not have an eligible health record. Do not deploy
  this migration elsewhere. The data owner must assess a verified backup before
  any recovery work starts.

## Feature map

### 1. Identity and account access

Current features:

- Registration.
- Login and logout.
- Email verification.
- Password reset and password confirmation.
- Profile updates.
- Two-factor authentication.
- Browser session management.
- Sanctum API tokens.
- Forced password change for the default password.
- Account lock and unlock.

Current assumptions:

- Every user has one `school_id`.
- Roles and permissions control most actions.
- A locked account cannot use the main dashboard.
- A student who graduates cannot use all dashboard features.

Questions to decide:

- Can one person work for more than one school?
- Who can create accounts: an administrator, an applicant, or both?
- Which account states do we need: active, invited, suspended, locked, archived?
- Does a password change remain mandatory after an administrator resets a password?
- Which actions require two-factor authentication?

Candidate direction:

- Keep Fortify and Sanctum.
- Add explicit account states and account audit events.
- Move account rules into named actions and policies.

Agreed decision:

- Allow administrators to create a person profile before account activation.
- Create a pending or invited account when a login is required.
- Assign school memberships and scoped permissions before activation.
- Send a one-time invitation link for the person to set a password and sign in.
- Do not use a shared default password for provisioned accounts.
- Expire and revoke invitation links.
- Keep the person profile and school records when an account is suspended or deprovisioned.
- Provision a pending student login when staff activate an enrollment by default.
- Send the student a one-time invitation when an enrollment requires self-service access.
- Reuse an existing person account instead of creating a duplicate login.
- Make account provisioning safe to retry.
- Allow an organization to disable student login provisioning for programs that do not need it.
- Allow organization administrators to create configurable field definitions.
- Allow campuses to create campus-specific fields when permitted.
- Do not share campus field definitions across campuses by default.
- Keep non-shared field values private to their owning scope by default.
- Allow approved non-shared fields in a transfer package as additional information.
- Label transferred custom-field values with their source campus and field definition.
- Defer configurable field-generation screens until core enrollment and school workflows are stable.
- Defer custom-field imports and exports with the field-generation feature.
- Keep current core fields explicit and stable during the first modernization phases.

Progress:

- Done: there is no public registration and no shared default password. An
  administrator provisions a person with `App\Actions\Identity\ProvisionAccount`,
  which reuses an existing account by email, so provisioning is safe to retry
  and one person keeps one login across schools.
- Done: `account_invitations` carry a one-time link that expires and can be
  revoked (`SendAccountInvitation`, `AcceptAccountInvitation`,
  `RevokeAccountInvitation`, and the hourly `PruneExpiredInvitations`).
- Done: `App\Enums\AccountStatus` holds the account states, and
  `ChangeAccountStatus` is the only way through them. Suspending an account
  keeps the person and the school records.
- Done: every role, permission, and account-state change is written to
  `audit_events` by `RecordPermissionChanges` and `RecordAccountStatusChange`.
- Open by decision, not by oversight: configurable field definitions and their
  imports and exports stay deferred, as this section says.

### 2. Schools, tenancy, and administration

Current features:

- Create, edit, view, and delete schools.
- Select the active school for a super administrator.
- Create and manage administrators.
- Manage roles and permissions through Spatie Permission.
- View dashboard statistics for the active school.

Current assumptions:

- A super administrator changes the active school by changing their own `school_id`.
- Most services read the school from `auth()->user()`.
- Each query must remember to add a school condition.
- A school with users cannot be deleted.

Risks:

- School isolation is spread across services, requests, Livewire components, and policies.
- A missed condition can expose data from another school.
- [SchoolPolicy](app/Policies/SchoolPolicy.php) contains an assignment in an authorization condition.
- [UserPolicy](app/Policies/UserPolicy.php) contains the same class of defect in account locking.

Questions to decide:

- Is `school_id` an active school, a membership, or both?
- Can a user belong to several schools with different roles?
- Can a user switch school without signing in again?
- Can schools be archived instead of deleted?
- Do we need a platform administrator role above school administrators?

Candidate direction:

- Introduce a school context object for the current request.
- Add explicit school memberships if multi-school users are required.
- Centralize school scoping and test cross-school access for every resource.

Agreed decision:

- Keep one global person identity and one shared login per person.
- Represent school access with school membership records.
- Store roles and permissions per school membership.
- Store the active school in the session or request context.
- Do not change a user record to switch the active school.
- Add teaching assignments for school, academic period, subject, class, and section.
- Limit teacher access by school membership, role, and teaching assignment.
- Represent student participation with school enrollment records.
- Permit concurrent student enrollments when the business allows them.
- Mark one enrollment as primary for organization-level workflows when needed.
- Treat movement between campuses in one organization as an internal placement change.
- Treat movement to another organization as a formal transfer.
- Keep old school records when a student transfers.
- Share transfer data only through an approved transfer process.
- Make transfer operations safe to retry without duplicate enrollments.
- Keep permission keys stable and controlled by the application.
- Make roles creatable, editable, duplicable, and archivable.
- Scope role assignments to a platform, organization, or campus.
- Allow one person to have different roles at different campuses.
- Protect critical platform roles and audit role changes.
- Do not use editable role names to control business behavior.
- Use enrollment for student status and teaching assignments for teacher access.
- Treat each role as a resource owned by an organization or campus scope.
- Allow role management only when the actor has role-management permission in that scope.
- Limit role assignment to users inside the actor's managed scope.
- Limit new role permissions to the permissions the actor already holds in that scope.
- Prevent a school administrator from creating, editing, or assigning roles for another school.
- Keep platform permissions outside school-level role management.
- Control enrollment and transfer actions with stable permissions.
- Provide default role templates for common enrollment and transfer responsibilities.
- Treat default roles as editable starting points, not hardcoded business logic.
- Keep the built-in role set small for smaller organizations.
- Provide only Organization Administrator, Teacher, Student, and Guardian as default access profiles.
- Let organizations create Registrar, Finance Officer, Campus Administrator, and other roles when needed.
- Treat built-in profiles as permission templates, not role names used by business logic.
- Use separate permissions for enrollment creation, approval, release, acceptance, and completion.
- Enforce role scope and teaching or administrative assignments during each action.
- Support an organization-wide overview when a person has multiple enrollments or memberships.
- Let the person select a current working school or campus for detailed work.
- Resolve a subdomain or custom domain to an organization and campus context.
- Treat the domain as a context hint, not as proof of authorization.
- Verify membership or enrollment before serving a scoped request.
- Store the current working context in the session or request, never by changing the user identity.
- Require a specific school or campus context before a write operation.
- Open staff users in their last-used working school or campus.
- Open students and parents in an organization overview when they have multiple enrollments.
- Keep writes unavailable in organization overview mode.
- Require a working school or campus for payments, marks, enrollment changes, and administration.
- Show separate totals and records for each school or campus.
- Display the school and campus on every organization-wide record.
- Treat the organization or district as the data ownership boundary.
- Treat a campus as a location within an organization.
- Treat a deployment as infrastructure, not as a data ownership boundary.
- Attach student financial accounts to an explicit billing organization or billing group.
- Carry financial debt across campuses when they share the same billing organization.
- Do not move financial debt automatically between different organizations.
- Allow an external transfer to include a debt notice or approved obligation without creating a destination invoice automatically.
- Allow districts with separate campus ledgers to configure separate billing groups.
- Separate direct record access from data-sharing requests.
- Do not treat shared organization ownership as unrestricted record access.
- Share identity, guardians, enrollment status, and approved academic data according to role scope.
- Restrict health, discipline, safeguarding, counseling, internal notes, and detailed financial data by default.
- Add an auditable data-sharing request with requested categories, purpose, expiry, decision, and fulfillment record.
- Keep request, approval, and fulfillment permissions separate.
- Allow same-organization campus movement without creating a duplicate person.
- Use a formal transfer package for movement to another organization.
- Store received transfer data as a source-labelled snapshot.
- Keep original records owned by the source organization.

Progress:

- Done: one person, one login. `school_memberships` grant access to a school,
  `GrantSchoolMembership` and `EndSchoolMembership` change it, and switching
  school never writes to the user record.
- Done: `App\Services\School\SchoolContext` holds the working school in the
  session, `current_school_id()` is the one place that turns it into a query
  condition, and `App\Traits\InSchool` fills and scopes it. `SchoolScopeTest`
  fails the build if a new school-owned model forgets the trait.
- Done: permissions are school-scoped through Spatie teams. Roles are data,
  never business logic: enrollment decides student status and teaching
  assignments decide teacher access.
- Done: `RequireActiveSchool` demands a working school before a write, and
  every controller checks its policy against the working school.
- Done: an internal move is a placement change (`ChangeEnrollmentPlacement`)
  and a move to another school is a formal transfer (`TransferEnrollment`),
  which keeps the old enrollment and its history and names the enrollment it
  continues.
- Done: sharing records between schools is a request, not an assumption.
  `data_sharing_requests` names the categories, the reason, and the day the
  permission ends; `App\Enums\DataCategory` keeps health, discipline,
  safeguarding, wellbeing, and detailed finance closed unless a request names
  them. Asking, approving, and handing over are three separate permissions.
  `transfer_packages` is the source-labelled copy that was actually handed
  over: written once, read by the receiving school as a snapshot, and taken in
  by an explicit act.
- Done: the hierarchy now has three distinct levels. System Administrator is
  the global Spatie `platform-admin` role with explicit platform permissions
  in the reserved system team. An active `organization_memberships` record
  limits scope, and the global Spatie `organization-admin` role grants the
  Organization Administrator setup permissions without granting school data
  access. School Administrator stays the existing `admin` role inside
  Spatie's `school_id` team scope.
- Done: legacy schools are safely given one private organization each during
  the forward migration. They are not automatically grouped, so the migration
  does not create cross-campus visibility by guesswork. A later, audited
  `AssignSchoolToOrganization` action groups campuses when an authorized
  person explicitly does so.
- Done: Organization and school setup screens plus dashboard context show the
  ownership hierarchy. The organization dashboard states that operational data
  remains school-scoped.
- Open: subdomain and custom-domain context, the organization-wide overview
  screen for people with several enrollments, billing groups across campuses,
  delegated organization permissions beyond organization administration, and
  role management screens.

### 3. Academic setup and calendar

Current features:

- Academic years.
- Semesters.
- School-level active academic year and semester.
- Class groups.
- Classes.
- Sections.
- Subjects.
- Grade systems.
- Weekdays.

Current assumptions:

- A school has one active academic year and one active semester.
- Many dashboard features cannot run until the active year and semester exist.
- A student has one current class and section.
- Academic history is stored in a student-to-academic-year pivot.

Questions to decide:

- Can an academic year have more than two terms?
- Can schools use different calendars?
- Can a student change section during a term?
- What happens when an academic year closes?
- Which records become immutable after closure?

Candidate direction:

- Model academic periods as ordered, configurable periods.
- Define explicit open, closed, and archived states.
- Keep current placement separate from historical placement.

Progress:

- Done: periods are ordered and configurable. A `semesters` row now holds a
  `type` (`App\Enums\AcademicPeriodType`), a `position`, and optional
  `starts_on` and `ends_on` dates. A school can run terms, semesters,
  trimesters, or quarters.
- Done: a new period takes the next place in its year by itself, and
  `AcademicYear::semesters()` reads them in teaching order.
- Done: two periods of one year cannot share a day.
  `SemesterService` refuses an overlap, a backwards period, and a period with
  only one date.
- Done: `AcademicYear::periodForDate()` answers which period covers a day, and
  the working context falls back to it when nobody chose a period.
- Done: periods have `draft`, `open`, and `closed` states, and closing freezes
  the records of the period. See feature 20.
- Open: class offerings for a period, and a campus-specific calendar. Both
  wait for the campus model.

### 4. Admissions and people

Current features:

- Account applications.
- Rejected application list.
- Students.
- Teachers.
- Parents.
- Administrators.
- Parent-to-student assignment.
- Student profile pages.

Current assumptions:

- A person's role is represented by a permission package role.
- A student is a user plus a student record.
- A parent relationship is stored through a pivot record.
- Admission numbers are generated by a random loop.
- Applicant status changes control the admission flow.

Questions to decide:

- Is a person allowed to hold several roles?
- Is a guardian different from a parent?
- Can one student have several guardians with different permissions?
- What documents and review steps does an application require?
- Can an admitted applicant become a student without creating a second identity?
- Do we need bulk import and export?

Candidate direction:

- Separate person identity, user access, and student enrollment.
- Define an application state machine.
- Add database uniqueness for admission numbers.
- Add an admission audit trail.

Agreed decision:

- Make multi-campus applications configurable per organization.
- Make waitlists configurable per organization or campus.
- Use application status to control the admission workflow.
- Keep application status separate from enrollment status.
- Keep account status separate from both application and enrollment status.
- Do not activate an enrollment from an unapproved application without an explicit override permission.
- Keep waitlist entries with priority, position, capacity, and decision history.

Deferred for later:

- Multi-campus application workflows.
- Waitlists and capacity rules.
- Multi-stage admission decisions.
- Advanced configurable field generation.

Progress:

- Done: identity, access, and enrollment are three records, not one. A person
  is a `User`, access to a school is a `SchoolMembership`, and learning in a
  school is a `StudentRecord`.
- Done: admission numbers are unique per school in the database, not by a
  random loop hoping for the best.
- Done: account status, enrollment status, and application status are kept
  apart. `ChangeEnrollmentStatus` writes every enrollment change to
  `enrollment_status_changes` and to the audit log.
- Done: an admitted person never gets a second identity, because provisioning
  reuses the account it finds by email, and the student importer follows the
  same path.
- Done: bulk import exists for students and staff, checked before it writes
  and safe to run twice (§19).
- Open by decision: multi-campus applications, waitlists, capacity rules, and
  multi-stage admission decisions stay deferred, as this section says.

### 5. Student lifecycle

Current features:

- Admit a student.
- Assign a class and section.
- Promote students.
- Graduate students.
- Reset promotion.
- Reset graduation.
- View current and historical student records.

Current assumptions:

- Graduation is a boolean on the current student record.
- Promotion updates the current record and writes a history pivot.
- Reset operations can reverse previous actions.
- A global scope hides graduated student records by default.

Questions to decide:

- What are the complete enrollment states: applicant, enrolled, transferred, withdrawn, graduated, archived?
- Can a student repeat a year?
- Can a student be promoted to a different class and section at the same time?
- Can a promotion be reversed after reports or invoices exist?
- What is the transfer process between schools?

Candidate direction:

- Replace boolean lifecycle fields with explicit enrollment events or states.
- Keep an append-only placement history.
- Require a reason and actor for lifecycle changes.

Agreed decision:

- A person/profile can exist before an active enrollment. Enrollment is the core unit that connects a person to an organization, campus, academic period, class, and section.
- The core workflow is: create or find the person, create the enrollment, assign its school/campus and academic placement, then provision a pending student login when that program allows it.
- A person may have concurrent enrollments. A primary enrollment may be selected for convenience, but it must not hide the other enrollments.
- Internal movement between campuses is a placement change within the same organization. Movement to another organization is a formal transfer that creates a new enrollment while preserving the source history.
- Use explicit enrollment states such as `active`, `suspended`, `withdrawn`, `transferred`, `graduated`, and `archived`; do not use one mutable boolean or a global scope to represent lifecycle history.
- Placement and status changes are append-only records with an effective date, actor, reason, and source enrollment. Repeating a request must be safe and must not create duplicate history.
- Access to create, change, suspend, transfer, graduate, or archive an enrollment is permission-based and scoped to the relevant organization/campus. Existing records remain readable according to their historical scope.
- Waitlists, capacity rules, multi-stage applications, and advanced admission configuration remain deferred. The first implementation focuses on reliable profiles, enrollments, placements, transfers, and history.

Progress:

- Done: an enrollment holds an explicit state. `student_records.status` is cast
  to `App\Enums\EnrollmentStatus` and the `is_graduated` boolean and its global
  scope are gone.
- Done: every state change is append-only in `enrollment_status_changes`, with
  the actor, the reason, and the effective date. The record refuses updates and
  deletes.
- Done: `App\Actions\Enrollment\ChangeEnrollmentStatus` is the only way to
  change a state. It checks the transition and ignores a repeated request, so a
  retry is safe.
- Done: an enrollment names its own school, so a person can hold several
  enrollments at once. `User::studentRecords()` lists them all, and
  `User::studentRecord()` picks the primary one of the school being worked in
  without hiding the others.
- Done: placements are their own append-only history in
  `enrollment_placements`. `App\Actions\Enrollment\ChangeEnrollmentPlacement`
  writes admission, promotion, and promotion reset. It refuses a section
  outside the class, a class of another school, a closed academic year, and a
  closed enrollment.
- Done: `App\Actions\Enrollment\TransferEnrollment` closes the old enrollment,
  opens the new one in the destination school, and keeps the source history
  through `transferred_from_id`.
- Open: campus placement changes, which wait for the campus model.

### 6. Curriculum and teaching assignments

Current features:

- Maintain one subject catalog per school.
- Create period-specific course offerings for the catalog subjects.
- Assign teachers through dated teaching assignments.
- Upload syllabi for one exact course offering.

Current assumptions:

- A catalog subject belongs to one school, not to a class.
- An offering supplies the academic level, period, roster, and teaching context.
- A syllabus identifies its offering; its subject and period are derived, never copied.

Questions to decide:

- Can the same subject have different content by class or academic year?
- Can several teachers share a subject?
- Do we need lesson units, outcomes, or attendance requirements?
- Should a teacher assignment be limited to a section?

Candidate direction:

- Introduce teaching assignments scoped to academic period and class.
- Keep syllabus versions by period.

Agreed decision:

- The organization owns the default academic calendar. A campus may share that calendar or use a campus-specific calendar when its schedule differs.
- An academic calendar contains ordered, configurable periods with names, types, dates, and lifecycle status. Terms and semesters are defaults, not fixed application concepts.
- Class definitions are reusable. Class offerings and sections are created for a specific academic period, and enrollment placements reference those period-specific offerings.
- Academic periods use explicit `draft`, `open`, and `closed` states. Closing a period freezes placements, timetable changes, and academic results. Reopening requires permission and an audit record.
- Financial records have their own posting and closing lifecycle; closing an academic period does not automatically close invoices or payments.
- School-level current-year and current-semester pointers must not be the source of truth. The working context is selected per request/session, while every academic record keeps an explicit period reference.

### Curriculum and teaching decisions

- Each campus selects an instructional model for an academic cycle. The model sets defaults, visible screens, validation rules, and report labels. It does not create a separate data model.
- Setup asks one plain-language question: "Do learners normally remain with one class group through the day?" It does not ask staff to select a country or education system.
- The system provides these instructional model presets:
  - `FixedHomeSections` is the default. Learners stay in one home section. Subject rosters derive from that section. Teachers usually move between sections.
  - `Hybrid` keeps home sections as the default. Selected offerings support combined sections or individual rosters.
  - `SubjectBasedSchedule` uses individual subject rosters. Each learner can attend different offerings during the day.
- The system allows a campus to enable a limited exception for an offering, such as a combined music class or a remedial group. An exception does not change the campus model.
- Change the instructional model only for a future academic cycle. A change for an active cycle requires a dedicated, audited migration workflow. Do not expose it as a normal settings edit.
- Keep the universal home-section and instructional-group model for every preset. Do not fork workflows or records by preset.
- Subjects are reusable organization-level catalog records with stable codes and names. Campuses and programs can offer the same subject differently.
- Keep a learner's home section separate from an instructional group. A home section is the learner's stable group for registration, homeroom, reports, and general communication. An instructional group is the roster for one subject offering.
- Use `AcademicLevel` and `AcademicCycleSection` as the internal concepts. The user interface uses each school's local labels, such as Class, Grade, Form, Year, Stream, or Homeroom.
- Create an academic cycle section for one campus and one academic cycle. Do not reuse an academic cycle section for a later cycle.
- Teaching happens through period-specific subject offerings. An offering identifies the campus, academic period, academic level, subject, and one or more instructional groups.
- An offering supports these roster modes: one home section, combined home sections, all learners in an academic level, or an explicit individual roster.
- Use a home-section roster by default. This supports schools where learners stay together and teachers move between sections. Use an individual roster for electives and schools where learners move between subjects.
- Teaching assignments include the teacher membership, offering, role (such as lead or supporting teacher), effective dates, and status. Multiple teachers may share an offering.
- A class-wide assignment applies to its sections by default. A section-specific assignment can override or supplement it.
- Syllabi are versioned by subject offering and academic period. Published versions remain available when a later version is created.
- Teachers can manage only the offerings within their assignment scope. Curriculum managers can manage catalog records and assignments according to their permissions.
- Detailed learning outcomes, lesson units, competency frameworks, and attendance weighting are deferred until the core assignment model is stable.

Progress:

- Done: teaching is a dated assignment, not a pivot row. A
  `teaching_assignments` record names the school, subject, teacher, academic
  year, period, optional academic cycle section, role
  (`App\Enums\TeachingRole`), start date, and end date. Several teachers can
  share one subject.
- Done: `App\Actions\Curriculum\AssignTeacher` is the only way in. It refuses
  a person who is not a teacher, a teacher or cycle section of another school,
  a cycle section outside the assignment year, and a closed year. Asking twice
  returns the assignment that already runs.
- Done: ending an assignment keeps the record and gives it an end date, so
  last year's timetable still says who taught.
- Done: `course_offerings` is the dated subject record. It keeps its school,
  academic cycle, academic period, academic level, roster, and lifecycle
  state. It does not change enrollment placement.
- Done: one offering uses one or more exact academic cycle sections, a whole
  academic level, or named learner enrollments. The campus instructional model
  validates the roster before the offering is created.
- Done: a teaching assignment can name a course offering. Lead and supporting
  teachers stay dated, audited, and readable after the period ends.
- Done: reusable `academic_levels` support local labels, display order, and
  parent levels.
- Done: `academic_cycle_sections` belong to one school, one academic cycle,
  and one academic level. They keep stream, shift, language, room, capacity,
  homeroom teacher, and lifecycle state.
- Done: staff can roll section structure into another cycle. The new sections
  start as drafts. The action never copies learners, teachers, or old links.
- Done: the subject catalog has no class foreign key. `syllabi` now reference
  `course_offerings`, so their subject, level, and academic period cannot
  disagree.
- Agreed breaking change: `AcademicLevel` and `AcademicCycleSection` replace
  `MyClass` and `Section`. Do not keep bridge columns, bridge relations, or
  parallel read paths. All new and changed operational records use the new
  identifiers only.
- Done: assessment work happens in the course-offering gradebook. The former
  class-bound exam-record, tabulation, and result-checker paths are removed.
- Open: replace the remaining `subject_user` compatibility pivot with dated
  assignment queries. It must not remain a second source of truth.
- Open: section-level timetable overrides and published syllabus revisions by
  offering.

### 7. Timetable

Current features:

- Timetables.
- Time slots.
- Timetable records.
- Custom timetable items.
- Timetable management and printing.

Current assumptions:

- A timetable is mainly associated with a class.
- Time slots and weekdays are fixed enough for the current workflow.
- Custom items fill gaps outside the normal subject schedule.

Questions to decide:

- Does the timetable belong to a class, section, room, teacher, or all four?
- How do we prevent two classes from using one room at the same time?
- How do we prevent one teacher from having two lessons at the same time?
- Do we need substitutions, holidays, and recurring exceptions?
- Is the timetable effective for one term or the full year?

Candidate direction:

- Add conflict validation as a domain service.
- Scope timetable versions to an academic period.
- Treat changes as revisions instead of overwriting published schedules.

Agreed decision:

- Timetables are scoped to a campus and academic period. Sections are the scheduling truth, with class-level templates inherited by sections when appropriate.
- A timetable entry records its class or section, subject offering when applicable, assigned teacher when applicable, optional room/resource, weekday, start/end time, effective date range, and entry type.
- The timetable supports both instructional and non-instructional entries. General assembly, breaks, lunch, clubs, events, activities, study periods, and other free items are first-class entries with a name, type, and optional description; they do not require a subject or teacher.
- Recurring weekly schedules use an effective date range. Published schedules are immutable revisions with `draft`, `published`, and `archived` states.
- Publishing validates conflicts for sections, teachers, and configured rooms. A room is optional, but a room conflict is rejected when one is assigned.
- Substitutions are dated overrides with a replacement teacher, reason, actor, and approval. They do not mutate the normal timetable.
- Timetable edits after publication create a new revision instead of changing historical schedules.

Progress:

- Done: a timetable is a revision with a state (`App\Enums\TimetableStatus`:
  `draft`, `published`, `archived`), a revision number, an optional section, an
  effective date range, and a record of who published it and when.
- Done: publishing checks the clashes first.
  `App\Services\Timetable\TimetableConflictChecker` refuses overlapping time
  slots in one timetable and a teacher who would teach two classes at the same
  time in the same period.
- Done: a published revision stops changing. The model refuses edits and
  deletes, and its time slots refuse writes.
  `App\Actions\Timetable\ReviseTimetable` copies it into the next draft, and
  publishing that draft archives the revision it replaces.
- Open: rooms and room clashes, substitutions as dated overrides, and
  section-level inheritance from a class template.

### 8. Examinations and results

Current features:

- Optional exam schedules.
- Exam schedule slots.
- Grade systems.
- Course-offering gradebooks and published result revisions.

Current assumptions:

- Exams describe a schedule. They do not contain marks or publish results.
- Teachers record every assessment in the assigned course offering gradebook.
- Published results are immutable gradebook snapshots, not an exam flag.
- Grade rules apply to the course offering and its roster.

Questions to decide:

- What is the grading model: percentage, points, bands, or mixed?
- Can marks be changed after publication?
- Who approves results before publication?
- Do we need moderation, remarks, ranking, or attendance weighting?
- How do we handle missing, absent, exempt, and incomplete marks?
- Which result versions must remain available after correction?

Candidate direction:

- Model result status and approval separately from publication.
- Store mark changes in an audit trail.
- Add database constraints for duplicate exam records.
- Move tabulation rules into dedicated result services.

Moodle-inspired grading proposal:

- Treat the gradebook as a tree: subject offering, nested grade categories, grade items, and individual grade entries. Moodle uses grade items, categories, and grades as its core building blocks, and supports both activity-linked and manually created items ([Moodle grade items](https://docs.moodle.org/24/en/Grade_items), [Moodle grade categories](https://docs.moodle.org/28/en/Grade_categories)).
- Keep the academic period as the mandatory reporting boundary, but let teachers create flexible assessment periods inside it, such as weekly work, continuous assessment, midterm, project, or final exam.
- Allow manual grade items with a name, date, maximum, grading type, optional category, weight, and notes. Start with numeric values, named scales, and text/comments; do not force every assessment into a percentage.
- Allow categories to use configurable aggregation such as weighted mean, simple mean, sum, or highest result. Moodle normalizes item ranges before aggregation, which allows items with different maximums to be combined ([Moodle grade aggregation](https://docs.moodle.org/400/en/Grade_aggregation)).
- Store missing, absent, exempt, incomplete, and not-applicable as explicit grade states. An empty value must not silently mean zero or missing work.
- Separate teacher grade entry from official result publication. Teachers may revise grades while the gradebook is open; publication creates a result snapshot, and later corrections create an audited revision instead of mutating the published result.
- Allow school-level assessment templates and reporting rules, while permitting teacher-level categories and items inside the assigned offering. This gives flexibility without losing consistent report cards.
- Do not allow a teacher to grade outside their offering, campus scope, or an academic period that is closed unless an authorized correction workflow is used.

Agreed decision:

- The academic period remains the official reporting boundary, but teachers can create flexible assessment periods and grade items within their assigned subject offering.
- Gradebooks support nested categories, manual grade items, numeric values, named scales, text/comments, different maximums, weights, and configurable aggregation.
- Missing, absent, exempt, incomplete, and not-applicable are explicit states rather than implicit zeroes or empty values.
- Teacher editing and official publication are separate states. Publication creates a result snapshot; later corrections create audited revisions.
- School-level assessment templates and reporting rules provide consistency, while teachers retain flexibility inside their assigned offering.
- Grade access remains scoped to the teacher's offering, campus, and open academic period. Corrections to closed periods require an authorized workflow.
- Exams are optional assessment types, not mandatory workflow objects. A teacher may use exams, quizzes, assignments, projects, classwork, observations, or any other assessment item supported by the gradebook.
- The system enforces only the minimum reporting structure: an academic period, a subject offering, a student enrollment, and permission to enter or publish grades. It must not require a fixed number of exams, exam slots, categories, or weighting scheme.
- Instructors may design their own gradebook inside that scope. Organization administrators may provide templates or reporting requirements, but templates do not force every instructor to use the same assessment structure unless an organization policy explicitly requires it.
- A published result is a calculated snapshot of the instructor's gradebook for the academic period. It does not depend on an `Exam` record existing.

Progress:

- Done: the gradebook is a tree. `grade_categories` hold `grade_items`, and
  each student has one `grade_entries` row per item. An item can be numeric, a
  named scale, or a comment, with its own maximum, weight, and due date.
- Done: named scales are school-owned reusable option sets, not class-bound
  percentage ranges. Every scale option can be descriptive or all options can
  carry configured points; a grade entry selects the exact option rather than
  storing free text. Options already used in learner records are immutable,
  while schools may add new options or deactivate a scale for future work.
- Done: missing, absent, exempt, incomplete, and not applicable are explicit
  states (`App\Enums\GradeEntryState`). Excused work leaves the total alone
  and missing work counts as nothing, so an empty box never quietly means zero.
- Done: `App\Services\Gradebook\GradebookCalculator` turns each item into a
  share of its own maximum, so items with different maximums combine.
  Categories aggregate by weighted mean, simple mean, sum, or highest result.
- Done: grade categories, grade items, and published result snapshots identify
  one exact `course_offering_id`, never a loose subject/year/period tuple.
  The portal, transfer package, graduation checks, rankings, and closure
  readiness query that offering, so results from different levels, rosters, or
  reporting periods cannot merge by accident.
- Done: `App\Actions\Gradebook\RecordGrade` is the only way to write a mark.
  It refuses a mark above the maximum, a graded entry without a number, a
  student of another school, a student outside the offering's declared roster,
  and any writing in a closed period. Publication applies the same roster
  guard before it writes an official result.
- Done: publication is separate from grading.
  `App\Actions\Gradebook\PublishResult` copies the calculation into an
  append-only `result_snapshots` row with a revision number. A correction
  publishes the next revision; the earlier one never changes.
- Done: the breaking schema migration discards old gradebook records that
  cannot name an exact offering before making that relationship mandatory.
  There is no raw subject/year/period compatibility path.
- Done: each course offering now has one gradebook workspace. Authorized staff
  add assessments, record working grades inline for the offering roster, and
  publish official result revisions from the same screen. `read gradebook`,
  `manage gradebook`, and `publish result` are school-scoped Spatie
  permissions; teachers additionally need an assignment to that offering.
- Done: legacy exam-record, tabulation, result-checker, and class-bound grade
  system screens, routes, permissions, data, and schema are removed. Exam
  schedules remain optional.
- Done: authorized staff can save an existing course-offering gradebook as a
  reusable school assessment template and apply it only to a blank gradebook in
  the same school. Templates copy categories, aggregation, item types, named
  grading scales, points, weights, and ordering; they never copy learner marks,
  due dates, or published results. Each use is audited, and an unavailable
  grading scale blocks application rather than creating an invalid assessment.
- Done: report cards are immutable, cross-subject snapshots. Authorized staff
  publish one only while its period is closing or finished; it captures the
  latest published result revision from each subject, an overall average, the
  source IDs and revisions, actor, timestamp, and reason. A correction makes
  a new report-card revision, and the report-card workspace lets staff issue,
  list, and inspect the frozen subject rows.

### 9. Fees, invoices, and payments

Current features:

- Fee categories.
- Fees.
- Fee invoices.
- Invoice line records.
- Waivers, fines, paid amounts, due and paid scopes.
- Invoice payment flow.
- Invoice printing.

Current assumptions:

- An invoice belongs to one student.
- Fees are created before invoices.
- Payments are stored as a value on invoice records.
- A waiver reduces the balance.
- A random invoice name identifies an invoice.

Questions to decide:

- Do we need a payment ledger instead of one mutable `paid` value?
- Can a payment be reversed or refunded?
- Can one payment cover several invoices?
- How do we handle discounts, scholarships, credits, and overpayments?
- Which currency and tax rules apply per school?
- Do invoices need a formal number sequence and receipt number?

Candidate direction:

- Separate invoice state, payment transactions, and allocation records.
- Add database uniqueness and a safe number generator.
- Make financial records append-only after payment.

Agreed direction:

- This is a school finance module, not a general-purpose ERP.
- Fees and student balances are accounts-receivable workflows backed by a simple double-entry ledger. Administrators use invoices, receipts, waivers, credits, refunds, expenses, and reports; they do not need to enter journal lines for routine operations.
- The organization or financial entity owns the books. Campuses are reporting dimensions or cost centres by default, so a school group can compare campuses without duplicating the ledger.
- Use a small configurable chart of accounts covering tuition and other income, receivables, cash and bank, scholarships/waivers, operating expenses, payables, assets, liabilities, and equity/opening balance.
- Every posted transaction produces balanced debit and credit lines, but the application generates those entries from approved school workflows.
- Posted transactions are immutable. Corrections use reversals, credits, refunds, write-offs, or adjusting entries with an actor, reason, and audit trail.
- Student invoices, waivers, scholarships, payments, refunds, and credits post through configured account mappings. Student balances come from invoice and allocation records, not a mutable `paid` total.
- Payments support partial allocation, one payment across multiple invoices, overpayments, unapplied credits, reversals, refunds, and write-offs.
- Support cash and bank accounts, common school payment methods, deposits, and basic reconciliation.
- Support budgets by campus, academic period, program, or fund, with budget-versus-actual reporting.
- Provide practical school reports: student balances and aging, income by fee type, expense reports, cash and bank summaries, general ledger, trial balance, income statement, balance sheet, and budget variance.
- Tax, payroll, inventory, procurement, fixed assets, statutory localization, and complex multi-currency are deferred or handled through integrations. They are not required for the core school finance module.

Progress:

- Done: a small double-entry ledger. `ledger_accounts` holds a starting chart
  of accounts that `App\Services\Finance\ChartOfAccounts` creates for each
  school and names by purpose, so the office never designs one to raise its
  first invoice.
- Done: `App\Actions\Finance\PostLedgerTransaction` is the only way in. It
  refuses an entry that does not balance, a line that is both a debit and a
  credit, and an entry that crosses two schools. Posted entries and their
  lines refuse updates and deletes.
- Done: corrections are reversals.
  `App\Actions\Finance\ReverseLedgerTransaction` posts the mirror entry, keeps
  both, and refuses to reverse the same entry twice.
- Done: the school workflows post by themselves. `ChargeStudent`,
  `RecordStudentPayment`, and `RelieveStudentFees` cover invoices, receipts,
  overpayments held as credit, scholarships, and write-offs. Creating a fee
  invoice now posts the charge.
- Done: `App\Services\Finance\StudentLedger` answers what a student owes from
  the lines, not from a stored `paid` total.
- Open: moving the invoice screens onto the ledger balance, payment
  allocation across several invoices, refunds, budgets, and the finance
  reports.

### 10. Notices and communication

Current features:

- Notices.
- Account status email notifications.
- Email verification.
- Password and account emails.

Current assumptions:

- Notices are a simple school-level content record.
- Email work uses the current synchronous queue default.

Questions to decide:

- Do notices target schools, roles, classes, sections, or individual users?
- Do we need read tracking and expiry dates?
- Which messages must be queued?
- Do we need SMS, push, or in-app notifications?

Candidate direction:

- Add audience and publication state to notices.
- Queue external communication.
- Add notification preferences.

Agreed decision:

- Notices and transactional account messages are separate. Verification, password, and account-status messages remain system notifications.
- Notices use `draft`, `scheduled`, `published`, `expired`, and `archived` states.
- Core audience targeting includes organization, campus, staff/student/guardian role, class offering, section, and selected users. Organization-wide and program-level targeting can be added without changing the model.
- Publishing creates recipient records so delivery, read, dismissed, and failed states can be tracked. Expired notices remain available in history.
- In-app delivery is the core channel. Email is queued and optional per notice; SMS and push are integration channels for later.
- Published content is revisioned. Editing a published notice creates a new revision or requires republishing.
- Users may control optional notifications, but security, account, and urgent school messages remain mandatory.
- Notice attachments use managed storage and authorization checks.

Progress:

- Done: notices have states (`App\Enums\NoticeStatus`: `draft`, `scheduled`,
  `published`, `expired`, `archived`) and keep the legacy `active` flag in step
  for the current screens.
- Done: publication writes a `notice_recipients` row per person, so the school
  can answer who was told and whether they read it
  (`App\Enums\NoticeRecipientState`). An expired notice keeps its recipients.
- Done: `App\Services\Notice\NoticeAudience` targets by role, class, section,
  or named people, and never reaches another school. An empty audience means
  everyone in the school.
- Done: email is optional per notice and leaves the request on the queue
  (`App\Jobs\SendNoticeEmails`). A failure is written to the recipient record.
- Done: `skuul:process-notices` publishes notices whose day arrived and
  expires the ones that ran out. It runs every fifteen minutes.
- Open: revisioning a published notice, attachments through managed storage,
  guardian-level targeting, and per-user notification settings.

### 11. Reports and exports

Current features:

- Student profile PDFs.
- Timetable PDFs.
- Fee invoice PDFs.
- Exam tabulation PDFs.
- DomPDF-based print service.

Current assumptions:

- Reports render directly from current Eloquent data.
- Reports are generated on demand.
- PDF generation can access remote assets.

Questions to decide:

- Which reports are official records?
- Do reports need a saved version and generation timestamp?
- Do schools need CSV or spreadsheet export?
- Should large reports run in a queue?

Candidate direction:

- Define report contracts and report data queries.
- Add report permissions and audit records.
- Queue large report generation.

Agreed decision:

- Reports are classified as operational, official, or export reports. Operational reports read current authorized data; official reports use a retained snapshot; exports are data files for authorized users.
- Official report records include the report type, organization/campus scope, academic or financial period, generator, timestamp, source revision or snapshot, and revision status.
- Core official reports include report cards and transcripts, enrollment and transfer history, invoices and receipts, fee statements, financial summaries, timetables, and gradebook exports.
- HTML and print CSS are the primary presentation layer. DomPDF is not part of the core architecture; downloadable PDFs use a replaceable, queued browser-based renderer.
- CSV exports are supported first. Spreadsheet exports can follow without changing report data contracts.
- Large reports and exports run asynchronously and notify the requester when ready.
- Report generation and downloads enforce organization/campus permissions and are audit logged.

Progress:

- Done: a report is a class behind `App\Contracts\Report`, listed in
  `App\Services\Report\ReportRegistry`. Adding a report is one class and one
  line; the request, the queue, and the download do not change.
- Done: `report_runs` records who asked, for what, with which parameters, when
  it finished, and where the file is. A failed report keeps the reason.
- Done: reports are built by a worker (`App\Jobs\BuildReport`) and written as
  CSV to storage, so a whole-school report never holds up a request.
- Done: the request and every download are permission-checked against the
  school and written to the audit log.
- Done: the first two reports are student balances, read from the ledger, and
  the class list.
- Done: transcripts are immutable, revisioned, audited lifetime snapshots of
  the latest official result in each subject offering across all academic
  periods. Staff can issue and list them through the transcript workspace;
  subsequent corrections issue the next revision rather than rewriting the
  prior document.
- Open: the queued browser-based PDF renderer and spreadsheet exports.

### 12. Platform operations

Current features:

- Sail and Docker development environment.
- Database seeders.
- School initialization command.
- Super administrator command.
- Log viewer.
- Debugbar and query detector in development.
- Sanctum user API endpoint.

Current assumptions:

- Seeders provide much of the development dataset.
- The application runs as a single Laravel application.
- The queue defaults to synchronous processing.

Questions to decide:

- What is the supported deployment target?
- Do we need backups, restore checks, and retention rules?
- Which logs and audit records must be kept?
- Do we need background workers and scheduled commands?
- Do we need a public API or only internal browser routes?

Candidate direction:

- Add CI with tests, Pint, Larastan, and dependency audit.
- Document deployment and backup procedures.
- Move slow mail and report work to queues.

Agreed decision:

- Production uses a container-based deployment with managed database, object storage, cache, queue workers, and a scheduler. The deployment remains provider-neutral.
- Redis-backed queues are required for email, reports, exports, notifications, and other slow work. The synchronous queue is for local development only.
- Scheduled jobs handle notice expiry, reminders, reconciliation, cleanup, recurring billing, and other recurring work.
- Backups include both the database and uploaded files. Backups are encrypted, retained by policy, and restored regularly in a test environment.
- Health checks cover the database, cache, queue, storage, and scheduler. Structured logs and audit records cover enrollment, permissions, grades, finance, publication, and data sharing.
- Production seeders are safe and separate from demo data.
- CI checks tests, formatting, static analysis, dependency vulnerabilities, and migration safety.
- The first product surface is the authenticated browser application. A versioned public API is deferred until a real integration requires it.

Progress:

- Done: `App\Providers\MonitoringServiceProvider` reports slow queries, slow
  requests, and failed jobs. `HealthController` answers for the database,
  cache, queue, and storage, and `routes/console.php` writes a scheduler
  heartbeat the health check reads.
- Done: `skuul:check-backup` fails when a backup is missing or too old, so a
  backup that quietly stopped running is noticed.
- Done: slow work runs on the queue — invitation mail, notice mail
  (`SendNoticeEmails`), and reports (`BuildReport`). The scheduler runs notice
  processing, invitation pruning, the backup check, and queue pruning.
- Done: `OPERATIONS.md` documents deployment, rollback, backup, restore,
  monitoring, and CI. CI runs the tests, Pint, and Larastan.
- Done: `audit_events` covers permissions, account and enrollment status,
  publication, finance, features, and data sharing.
- Open: encrypted off-site backups and a restore rehearsal in a real
  environment, and the versioned public API, which stays deferred.

### 13. Attendance

Current state:

- No attendance domain is implemented.

Recommendation:

- Support daily attendance and section/class-period attendance as separate records.
- Use explicit statuses such as present, absent, late, excused, left early, remote, school activity, and not recorded.
- Record the actor, date, source, reason, correction history, and effective enrollment or section.
- Allow schools to enable only daily attendance, only section attendance, or both.
- Provide teacher entry, attendance correction workflows, attendance summaries, and guardian/student visibility according to permission.

Progress:

- Done: `attendance_records` holds one record per student per day, and one
  more per lesson when the school takes a lesson register
  (`App\Enums\AttendanceKind`). The two never overwrite each other.
- Done: `App\Enums\AttendanceStatus` covers present, absent, late, excused,
  left early, remote, school activity, and not recorded. A day nobody recorded
  stays out of the rate instead of counting against the student.
- Done: `App\Actions\Attendance\RecordAttendance` takes the register for one
  student or a whole list. It refuses a future day, a closed enrollment, a
  closed period, a lesson register with no subject, and a subject of another
  school.
- Done: a correction writes an append-only `attendance_changes` row with the
  actor and reason, beside the record it corrects.
- Done: `App\Services\Attendance\AttendanceSummary` counts present, absent,
  late, and excused days and works out the rate.
- Done: staff have a one-page daily home-section register. It restores marks
  already taken for the selected day, submits the entire roster through the
  audited attendance action, and is protected by school-scoped `read
  attendance` and `take attendance` permissions.
- Done: corrections after a period closes use the existing authorized
  academic-period reopen workflow. It requires a stated reason and writes the
  lifecycle audit event before the append-only attendance correction can be
  made; there is no attendance-specific bypass around a closed period.
- Done: schools configure `daily_register` and `lesson_register` independently
  through the existing attendance feature config. The attendance action reads
  those flags itself, so disabling a register blocks every write path while
  preserving earlier records.
- Open: guardian attendance screen authorization coverage.

### 14. Student and guardian portal

Current state:

- Parent and student profiles exist, but there is no complete self-service portal.

Recommendation:

- Let students and guardians view authorized grades, attendance, timetables, notices, invoices, receipts, and official documents.
- Support guardians with multiple children and students with multiple enrollments.
- Add requests for documents, corrections, appointments, and acknowledgements without granting write access to school records.
- Allow each organization to enable or disable portal areas independently.

Progress:

- Done: `App\Services\Portal\PortalAccess` answers who may read what. A
  student reads their own enrollments; a guardian reads the enrollments of the
  children recorded against them, however many there are and however many
  schools they attend. No portal read depends on a staff permission.
- Done: `App\Services\Portal\PortalSummary` reads only what the school
  published — the newest revision of each result snapshot, a published
  timetable, the notices actually sent to the person, and the invoices and
  balance from the ledger. Work in progress never reaches a family.
- Done: `App\Enums\PortalArea` gives each area its own switch inside the
  `portal` feature settings, so a school can close invoices and keep results.
  An area a school has not chosen stays open.
- Done: `portal_requests` is the one thing a family writes. A request for a
  document, a correction, an appointment, or an acknowledgement changes no
  school record until somebody at the school answers it, and
  `PortalRequestPolicy` never lets a family answer its own request.
- Open: the screens, document downloads, appointment times taken from the
  calendar, and per-guardian notification settings.

### 15. Discipline and safeguarding

Current state:

- No dedicated discipline or safeguarding domain is implemented.

Recommendation:

- Record incidents, involved people, actions, follow-ups, status, and restricted notes.
- Separate ordinary behavior records from highly sensitive safeguarding records.
- Scope access by role, campus, case assignment, and explicit permission.
- Preserve a complete audit history. Disabling the feature must hide new workflows without deleting existing cases.

Progress:

- Done: `incidents` records what happened, where, when, who reported it, and
  who handles it. `incident_participants` names the people and why they are
  named, `incident_actions` records what the school will do, and
  `incident_status_changes` is the append-only history of how the case moved.
- Done: ordinary behaviour and safeguarding are the same workflow with
  different access (`App\Enums\IncidentCategory`). A safeguarding case marks
  itself restricted, and `IncidentPolicy` opens it only to the permission
  holders, the handler, and the reporter. `Incident::readableBy()` applies the
  same rule to lists.
- Done: `App\Actions\Discipline\ReportIncident` records the case, moves it
  through its states with a reason, and refuses a case in the future, an
  impossible state change, or an action on a finished case.
- Done: the `discipline` feature switch hides the workflow without touching
  the cases already recorded.
- Open: the screens, restricted notes inside a case, and case assignment by
  campus.

### 16. Student support and wellbeing

Current state:

- No dedicated health, counseling, intervention, accommodation, or support-plan domain is implemented.

Recommendation:

- Support optional health and emergency information, accommodations, intervention plans, counseling referrals, and follow-up actions.
- Keep sensitive information separate from the general student profile.
- Allow support teams to access only the categories and campuses assigned to them.
- Make each support area independently configurable because schools differ in what they collect and who may view it.

Progress:

- Done: `student_health_records` keeps the facts the school needs in an
  emergency — conditions, allergies, medications, diet, and one emergency
  contact — outside the student profile. Reading a student does not open it.
  `read health record` and `update health record` do, and neither is given to
  the administrator role by default.
- Done: `support_plans` records accommodations, interventions, counselling,
  and health plans as one workflow (`App\Enums\SupportCategory`). A health or
  counselling plan marks itself confidential, and `SupportPlanPolicy` opens it
  only to the permission holders, the person who runs it, and the person who
  wrote it. `SupportPlan::readableBy()` applies the same rule to lists.
- Done: `support_plan_actions` holds the steps and who must do them,
  `support_plan_notes` is the append-only record of what was said, and
  `support_plan_status_changes` is the append-only history of how the plan
  moved.
- Done: `App\Actions\Wellbeing\ManageSupportPlan` opens a plan, moves it
  through its states, and refuses a closed enrollment, a review date before
  the start date, an impossible state change, or work on a finished plan.
  `App\Actions\Wellbeing\RecordHealthInformation` writes the health record and
  logs the field names only, so the audit log never becomes a second copy of
  it.
- Done: the `wellbeing` feature switch hides the workflow without touching the
  plans already recorded.
- Open: the screens, referrals to people outside the school, per-category
  access for support teams, and campus-level assignment.

### 17. Staff operations

Current state:

- Teacher records and teaching assignments exist, but staff operations are limited.

Recommendation:

- Add staff profiles, credentials, certifications, campus assignments, availability, leave, and assignment history.
- Keep employment and payroll integrations separate from teaching assignments.
- Let organizations enable only the staff modules they need.

Progress:

- Done: `staff_profiles` records employment — staff number, job, department,
  employment type, status, and the dates the person joined and left — per
  school, because one person can work in two schools.
- Done: `staff_credentials` keeps qualifications, certificates, and licences
  with their issue date, expiry date, and who checked them.
  `StaffCredential::expiringBefore()` lists the ones about to run out.
- Done: `staff_availabilities` holds the hours a person can work in an
  ordinary week. A person who lists no hours counts as free, so a school never
  has to fill in an hours table before it can plan.
- Done: `staff_leave_requests` and the append-only
  `staff_leave_status_changes` hold leave and how it was answered.
  `App\Actions\Staff\ManageStaffLeave` refuses backwards dates, days that are
  already asked for, a person who has left, and an impossible state change.
  `StaffLeaveRequestPolicy` lets a person ask for their own days but never
  agree to them.
- Done: `App\Services\Staff\StaffAvailability` answers the question the
  timetable asks — can this person take work at this time — from status,
  leave, and working hours together.
- Done: employment stays separate from teaching. Leave does not touch a
  teaching assignment; ending one is still the curriculum action's decision.
- Done: the `staff_operations` feature switch hides the workflow without
  touching the records already kept.
- Open: the screens, cover and substitution when somebody is away, leave
  balances by type, appraisals, and payroll integration.

### 18. Calendar and school events

Current state:

- Academic periods and timetables are planned, but holidays, closures, events, and appointments are not modeled as a shared calendar.

Recommendation:

- Add holidays, closures, special instructional days, assemblies, clubs, activities, parent meetings, and appointments.
- Connect calendar events to campuses, classes, sections, staff, students, and guardians where appropriate.
- Keep events distinct from lessons and grades. Non-instructional timetable items can link to a calendar event but do not require a subject.
- Allow each campus or organization to enable event categories independently.

Progress:

- Done: `calendar_events` holds holidays, closures, special days, assemblies,
  activities, parent meetings, appointments, and examinations, each with a
  type from `App\Enums\CalendarEventType` that says whether the school still
  teaches that day.
- Done: `calendar_event_audiences` limits an event to a class, a section, or a
  named person. An event with no audience row is for the whole school.
- Done: `App\Services\Calendar\SchoolCalendar` answers the three questions the
  rest of the system asks: what is on between two dates, is this a teaching
  day, and which days the school is closed. Only published events count.
- Done: events never cross a school, because `calendar_events` uses the school
  scope like every other record.
- Open: the screens, links from a timetable item to an event, per-campus
  category switches, and appointment booking.

### 19. Imports and integrations

Current state:

- The application has seeders and browser workflows, but no durable import or integration boundary.

Recommendation:

- Provide validated, previewable imports for people, memberships, enrollments, classes, staff assignments, grades, invoices, and payments.
- Make imports idempotent with external source identifiers and an import batch history.
- Support CSV first, then integrations for identity, banking, payments, messaging, payroll, or government reporting when required.
- Export transfer packages and authorized reports without exposing unrelated campus data.
- Keep integration connectors optional and independently enabled.

Progress:

- Done: `App\Contracts\Importer` is the whole boundary. An importer says what
  columns it needs, what a good row looks like, and how to write one row. It
  never reads a file and never writes an import record, so one engine checks,
  previews, and applies every kind of import.
- Done: `App\Services\Import\ImportRunner` checks a file before it writes
  anything. `import_batches` counts what will happen, `import_rows` keeps each
  line, what was wrong with it, and the record it wrote. A row that fails
  while writing is marked with its reason and never stops the rows around it.
- Done: imports are safe to run twice. A row with a `source_id` writes an
  `imported_records` link, so the same outside identifier always finds the same
  record and a repeated file changes it instead of copying it.
- Done: `App\Services\Import\CsvReader` reads CSV by column name, trimmed and
  lowercased, so a heading of "Email " and one of "email" are the same column.
- Done: `App\Imports\StudentImporter` names the class and section by name and
  places the student through the enrollment action, so an imported student has
  the same placement history as one typed in.
  `App\Imports\StaffImporter` makes the account and the employment record and
  gives nobody a teaching assignment.
- Done: `create import`, `read import`, and `apply import` are separate
  permissions, because preparing an import is not the same decision as
  changing the school's records with it. The routes sit behind the `imports`
  feature switch.
- Open: the screens, more importers (guardians, grades, invoices, payments),
  transfer packages out, and connectors for identity, payments, messaging, and
  government reporting.

### 20. Feature configuration and record freezing

Agreed direction:

- Features are configurable at organization level, with campus and program overrides where needed. Settings inherit from the broader scope and can be explicitly enabled or disabled at a narrower scope.
- Disabling a feature hides its navigation and blocks its routes and actions. It does not delete existing records, invalidate history, or prevent authorized reporting on retained data.
- Identity, authorization, audit logging, and core enrollment history are platform capabilities and cannot be disabled. Attendance, portals, discipline, support, staff modules, event categories, rankings, and integrations can be enabled independently.
- Feature settings include both an enabled flag and domain-specific policy configuration. Configuration changes are permission-checked and audited.

Progress:

- Done: `feature_settings` holds one row per feature per school, and a row
  with no school is the platform default that schools inherit. A school
  setting wins.
- Done: `App\Enums\Feature` lists only what a school may switch: attendance,
  the portal, discipline, wellbeing, staff operations, events, ranking, and
  imports. Ranking starts off. Identity, authorization, audit logging, and
  enrollment history are not listed, so they cannot be turned off.
- Done: `App\Services\Feature\FeatureManager` answers `enabled()` once per
  request and carries per-feature settings. Every change is audited.
- Done: the `feature:` middleware hides the routes of a feature that is off,
  and `feature_enabled()` answers the same question in a view or a service.
  Turning a feature off never deletes what the school recorded.
- Open: campus and program overrides, and the settings screen.

Record freezing:

- Records move through explicit lifecycle states such as draft, open, submitted, approved, published, posted, closed, frozen, or archived, depending on the domain.
- A freeze applies to a defined scope: organization, campus, academic period, financial period, feature, offering, class, section, or report snapshot. It must never be an unexplained global switch.
- Frozen records are read-only for ordinary users. The system keeps the original values and records who froze them, when, why, and what scope was affected.
- Corrections do not edit frozen data in place. They create a revision, reversal, adjustment, or replacement record linked to the original.
- Reopening is a separate permissioned action requiring a reason, actor, timestamp, and audit record. Emergency unlocks can be time-limited.
- Academic period closure freezes placements, timetable publication, and academic results according to policy. Financial period closure freezes posted finance records according to accounting policy. Official reports remain immutable snapshots.

Ranking policy:

- Ranking is an optional derived view, not a required part of enrollment, grade entry, or the student record.
- An organization may enable ranking at the academic period, class offering, section, subject offering, or instructor-managed result-group scope.
- A ranking policy defines eligibility, source gradebook/category, calculation method, tie handling, publication visibility, and whether students, guardians, teachers, or administrators may see it.
- The default is no ranking. Grades, comments, and official results remain valid whether ranking is enabled or disabled.
- Rank values are calculated from the applicable published result snapshot and are never used as the primary source of academic history.

### 21. Cohorts, programs, and graduation planning

Agreed decision:

- Add a first-class cohort model. A cohort is a named, scoped collection of people that is distinct from a class roster or section, such as a graduation year, scholarship group, watchlist, club, or ranking group.
- Add reusable programs and program participation records for clubs, interventions, support services, extracurricular activities, special programs, and other supplemental services.
- Program participation records include the person, organization/campus, program, dates, status, responsible staff, optional schedule, and restricted notes where required.
- Keep program participation independent from enrollment. A student can participate in several programs while enrolled in one or more schools.
- Add graduation plans with required subjects, credits, completion rules, exemptions, target completion period, and outcome history where the organization uses credit-based graduation.
- Keep graduation plans configurable per organization, campus, program, or cohort. Schools that do not use credits can disable the credit workflow and use completion requirements instead.
- Calculate ranking from a cohort, subject offering, class/section, or other authorized result group and a published result snapshot. Ranking is never a primary student, enrollment, or grade field.
- Keep surveys deferred as an optional feature. The future survey model should support reusable forms, scoped audiences, responses, publication windows, and privacy controls without coupling surveys to grades or enrollment.

Progress:

- Done: `cohorts` and `cohort_members` hold named groups that are not classes
  — graduation years, scholarship groups, clubs, watchlists, and ranking
  groups. A student joins through their enrollment; staff and guardians join
  as themselves. Leaving keeps the record, so a school can still see who was
  in a group last year. A watchlist marks itself private and needs
  `read restricted cohort`.
- Done: `programs` and `program_participations` record clubs, interventions,
  support services, and special programmes with dates, a schedule, and the
  member of staff who runs it. Taking part never touches enrollment, and a
  student holds one place in a programme at a time.
- Done: `graduation_plans`, `graduation_requirements`, and
  `graduation_exemptions` say what a student must finish.
  `App\Services\Graduation\GraduationProgress` reads only published result
  snapshots, so a plan never counts a mark a family has not seen. A school
  that does not count credits leaves `uses_credits` off and finishes the plan
  by its requirements alone.
- Done: `App\Services\Ranking\ResultRanking` works out a position when it is
  asked for, from a cohort or a class and published snapshots only. Equal
  averages share a position, a corrected result counts once at its newest
  revision, and nothing is written to the student, the enrollment, or the
  grade. Ranking stays off until a school turns the `ranking` feature on.
- Open: the screens, cohort-scoped and campus-scoped plan assignment,
  restricted notes on a participation record, outcome history for graduation,
  and surveys.

## Cross-feature decisions

Resolved:

1. School membership, active school context, and cross-school teaching.
2. Scoped, editable roles and permission-based role management.
3. Organization calendars, campus overrides, and academic period lifecycle.
4. Student enrollment states, concurrent enrollments, transfers, and immutable history.
5. Flexible gradebooks with optional exams and audited publication revisions.
6. School finance with a simple ledger, receivables, payments, budgets, and reports.
7. Targeted notices, recipient tracking, and queued delivery.
8. Official report snapshots, CSV exports, and queued browser-based PDFs.
9. Container deployment, Redis queues, scheduled jobs, backups, and audit logs.

Open before implementation:

1. Who can approve published results when an organization enables approval.
2. Data retention, archival, and deletion rules by record category.
3. The first production deployment target and operational recovery objectives.
4. The first report set required by the pilot schools.

## Delivery plan

### Phase 0: Protect the current system

Status: in progress.

- Fix assignment operators in authorization policies. Done.
- Add cross-school authorization tests for every resource. Done in
  `tests/Feature/CrossSchoolAccessTest.php`.
- Repair the test database configuration. Done. Sail PHPUnit explicitly sets
  `APP_ENV=testing`, `DB_CONNECTION=mysql`, and `DB_DATABASE=testing`, so the
  development `.env` database cannot be used by tests.
- Repair the Larastan configuration. Done. `phpstan-baseline.neon` holds the
  legacy typing backlog, so new errors fail the build.
- Run the full test suite in Sail. The previous result of 442 tests is stale.
  Record the current result and date after every release-readiness change.
- Run Pint, Larastan, and `composer audit` in CI. Done in
  `.github/workflows/laravel-tests.yml`.

Exit condition: security tests pass and the current full test suite runs from a
clean environment.

### Phase 1: Confirm the business model

Status: in progress.

- Review each feature section with a school administrator and a teacher.
- Write the valid states and transitions for each workflow.
- Mark required behavior, optional behavior, and future behavior.
- Decide which history must remain immutable.

Exit condition: the cross-feature decisions have written answers and the pilot
schools approve the required workflows.

### Phase 2: Establish domain boundaries

Status: done.

- Add a current school context abstraction. Done. `App\Services\School\SchoolContext`
  holds the school for the request, with the `school_context()`, `current_school()`,
  and `current_school_id()` helpers.
- Move school scoping into consistent query or policy boundaries. Done. Models
  with a `school_id` use the `App\Traits\InSchool` trait and are read with
  `Model::inSchool()`. Policies compare the record against the working school.
- Replace magic role strings with named permissions or enums where useful. Done
  for names: `App\Enums\Role` holds the built-in profiles. Moving the remaining
  role checks to permissions is still open.
- Extract high-risk operations into typed actions. Done. Account provisioning,
  invitations, account status, school membership, enrollment status and
  placement, transfers, teaching assignments, timetable publication, grades and
  result publication, ledger postings, notices, reports, attendance, incidents,
  support plans, staff leave, imports, cohorts, portal requests, and data
  sharing are all typed actions. The services that remain are readers and
  controllers' helpers, not the place where a rule lives.
- Add database constraints and indexes. Done for schools, memberships,
  invitations, class groups, classes, sections, subjects, student records, and
  every table added since: placements, teaching assignments, timetables,
  gradebook, ledger, notices, reports, attendance, features, incidents,
  calendar, support, staff, imports, cohorts, portal requests, and data
  sharing.

Exit condition: a new feature cannot read or write another school by missing one local `where` clause.

### Phase 3: Modernize vertical features

Status: the domain foundation is implemented. Feature readiness varies because
many workflows still have open screens, reports, and operational decisions.

Work in this order unless business review changes it:

1. School context and user access. Done.
2. Admissions and student enrollment. Done, including placement history,
   concurrent enrollments, and transfers.
3. Academic years, terms, classes, and sections. Done, with dated periods and
   closing rules.
4. Subjects and teacher assignments. Done, as dated assignments with a history.
5. Timetables. Done, with conflict checks, publication, and revisions.
6. Exams and results. Done, with a gradebook tree and append-only published
   result snapshots.
7. Fees, invoices, and payments. Done, on a double-entry ledger.
8. Notices and reports. Done, both queued.

Everything the feature map added afterwards followed the same shape:
attendance, feature configuration, discipline and safeguarding, the calendar,
student support, staff operations, imports, the portal, cohorts and graduation
planning, and data sharing between schools.

For each feature:

1. Define the user stories.
2. Define the states and invariants.
3. Add or update the data model.
4. Add an application action.
5. Add authorization and validation tests.
6. Update the existing Livewire screen.
7. Add the new UI only after the workflow is stable.

Exit condition: each release feature has documented rules, tests, and a working
screen. Do not describe other features as done.

### Phase 4: Improve the user interface

Status: in progress. The first release slice is identity and account access.

Resolved integration issue:

- April UI input-group now renders passed attributes once. The identity slice
  uses the standard input-group component again.

- Keep Blade and Livewire during domain changes.
- Adopt April UI as the component and visual language for the application. April UI is Blade, Livewire, Alpine, and Tailwind based, so it fits the current rendering stack without a Vue/Inertia rewrite.
- Integrate the active April UI development branch. Validate it in this
  application before every deployment.
- Resolve the Tailwind version contract with the active April UI branch before
  migrating a screen.
- Replace shared primitives with April UI components by workflow. A temporary
  adapter is allowed only when it has an owner, a removal condition, and a
  target release. Do not keep permanent duplicate primitives.
- Identify the screens with the most interaction and slowest workflows.
- Replace the dashboard, authentication, navigation, forms, tables, and feedback states first.
- Migrate feature screens by vertical slice. Start with identity and account
  access before changing more dashboard workflows.
- Share authorization and validation with the server; UI components must not contain business rules.

Exit condition: each migrated screen has feature tests, a rendered-page smoke
test, Chrome desktop and mobile checks, no critical accessibility failures, no
console errors, no failed page assets, and no duplicate business rules in
JavaScript.

### Frontend direction

Agreed decision:

- The first frontend modernization uses April UI with Blade, Livewire, Alpine, and Tailwind. A Vue/Inertia rewrite is no longer the default plan.
- April UI components replace the current ad-hoc Blade primitives while Livewire actions, validation, policies, and domain services remain intact.
- UI replacement is incremental and reversible. Remove a legacy primitive only
  after every screen that uses it has passed the release-readiness gates.
- The active April UI branch and Tailwind version are explicit integration
  inputs. Compatibility is tested in this application and is not hidden behind
  Composer workarounds.

### Phase 5: Operate the product

Status: partially complete.

- Add queues for email and large reports. Done. Redis runs the queue in Sail
  and in production, and `queue:prune-failed` keeps the table small.
  `App\Jobs\BuildReport` and `App\Jobs\SendNoticeEmails` run there.
- Add scheduled tasks for reminders and maintenance. Done in
  `routes/console.php`: the scheduler heartbeat, invitation pruning, the
  backup check, and queue table pruning.
- Add audit logs and backup checks. Done. `audit_events` records role,
  permission, account, enrollment, period, and result publication changes
  through `App\Actions\Audit\RecordAuditEvent`. `skuul:check-backup` reports
  a missing or stale backup.
- Add deployment documentation and CI gates. Done in `OPERATIONS.md` and
  `.github/workflows/laravel-tests.yml`.
- Monitor slow queries and failed jobs. Done in
  `App\Providers\MonitoringServiceProvider`.

Exit condition: the system has repeatable deployment, backup, restore, and
monitoring procedures that have passed a real restore rehearsal.

## Review format for each feature

Use this format during feature discussions:

- Users: who uses the feature?
- Goal: what result does the user need?
- Start state: what must exist first?
- Main flow: what steps does the user follow?
- Exceptions: what can go wrong?
- Permissions: who can view and change it?
- State: what states can the record have?
- History: what must never be overwritten?
- Reports: what output is required?
- Notifications: who must be informed?
- Data: what fields and relationships are required?
- Tests: which rules must always pass?

## First discussion

Start with **Schools, tenancy, and administration**. It controls data ownership for every other feature. Then review **Student lifecycle**, because it controls the core academic record.
