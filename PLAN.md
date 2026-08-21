# Skuul Modernization Plan

Status: Draft

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

### 6. Curriculum and teaching assignments

Current features:

- Create and manage subjects.
- Assign teachers to subjects.
- Create and manage syllabi.
- Link subjects to classes and class groups.

Current assumptions:

- A subject belongs to one school and one class.
- Teacher assignments are direct user-to-subject relations.
- Syllabus records are tied to a subject and semester.

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

- Subjects are reusable organization-level catalog records with stable codes and names. Campuses and programs can offer the same subject differently.
- Teaching happens through period-specific subject offerings. An offering identifies the campus, academic period, class offering or section, and subject.
- Teaching assignments include the teacher membership, offering, role (such as lead or supporting teacher), effective dates, and status. Multiple teachers may share an offering.
- A class-wide assignment applies to its sections by default. A section-specific assignment can override or supplement it.
- Syllabi are versioned by subject offering and academic period. Published versions remain available when a later version is created.
- Teachers can manage only the offerings within their assignment scope. Curriculum managers can manage catalog records and assignments according to their permissions.
- Detailed learning outcomes, lesson units, competency frameworks, and attendance weighting are deferred until the core assignment model is stable.

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

### 8. Examinations and results

Current features:

- Exams.
- Exam slots.
- Exam records and marks.
- Active and published result flags.
- Semester result tabulation.
- Academic-year result tabulation.
- Student and parent result checker.
- Grade systems.

Current assumptions:

- Marks are entered per student, subject, and exam slot.
- A teacher can enter marks when assigned to the subject.
- Results become visible when a publish flag is set.
- Grade rules are attached to a class group.

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

### 13. Attendance

Current state:

- No attendance domain is implemented.

Recommendation:

- Support daily attendance and section/class-period attendance as separate records.
- Use explicit statuses such as present, absent, late, excused, left early, remote, school activity, and not recorded.
- Record the actor, date, source, reason, correction history, and effective enrollment or section.
- Allow schools to enable only daily attendance, only section attendance, or both.
- Provide teacher entry, attendance correction workflows, attendance summaries, and guardian/student visibility according to permission.

### 14. Student and guardian portal

Current state:

- Parent and student profiles exist, but there is no complete self-service portal.

Recommendation:

- Let students and guardians view authorized grades, attendance, timetables, notices, invoices, receipts, and official documents.
- Support guardians with multiple children and students with multiple enrollments.
- Add requests for documents, corrections, appointments, and acknowledgements without granting write access to school records.
- Allow each organization to enable or disable portal areas independently.

### 15. Discipline and safeguarding

Current state:

- No dedicated discipline or safeguarding domain is implemented.

Recommendation:

- Record incidents, involved people, actions, follow-ups, status, and restricted notes.
- Separate ordinary behavior records from highly sensitive safeguarding records.
- Scope access by role, campus, case assignment, and explicit permission.
- Preserve a complete audit history. Disabling the feature must hide new workflows without deleting existing cases.

### 16. Student support and wellbeing

Current state:

- No dedicated health, counseling, intervention, accommodation, or support-plan domain is implemented.

Recommendation:

- Support optional health and emergency information, accommodations, intervention plans, counseling referrals, and follow-up actions.
- Keep sensitive information separate from the general student profile.
- Allow support teams to access only the categories and campuses assigned to them.
- Make each support area independently configurable because schools differ in what they collect and who may view it.

### 17. Staff operations

Current state:

- Teacher records and teaching assignments exist, but staff operations are limited.

Recommendation:

- Add staff profiles, credentials, certifications, campus assignments, availability, leave, and assignment history.
- Keep employment and payroll integrations separate from teaching assignments.
- Let organizations enable only the staff modules they need.

### 18. Calendar and school events

Current state:

- Academic periods and timetables are planned, but holidays, closures, events, and appointments are not modeled as a shared calendar.

Recommendation:

- Add holidays, closures, special instructional days, assemblies, clubs, activities, parent meetings, and appointments.
- Connect calendar events to campuses, classes, sections, staff, students, and guardians where appropriate.
- Keep events distinct from lessons and grades. Non-instructional timetable items can link to a calendar event but do not require a subject.
- Allow each campus or organization to enable event categories independently.

### 19. Imports and integrations

Current state:

- The application has seeders and browser workflows, but no durable import or integration boundary.

Recommendation:

- Provide validated, previewable imports for people, memberships, enrollments, classes, staff assignments, grades, invoices, and payments.
- Make imports idempotent with external source identifiers and an import batch history.
- Support CSV first, then integrations for identity, banking, payments, messaging, payroll, or government reporting when required.
- Export transfer packages and authorized reports without exposing unrelated campus data.
- Keep integration connectors optional and independently enabled.

### 20. Feature configuration and record freezing

Agreed direction:

- Features are configurable at organization level, with campus and program overrides where needed. Settings inherit from the broader scope and can be explicitly enabled or disabled at a narrower scope.
- Disabling a feature hides its navigation and blocks its routes and actions. It does not delete existing records, invalidate history, or prevent authorized reporting on retained data.
- Identity, authorization, audit logging, and core enrollment history are platform capabilities and cannot be disabled. Attendance, portals, discipline, support, staff modules, event categories, rankings, and integrations can be enabled independently.
- Feature settings include both an enabled flag and domain-specific policy configuration. Configuration changes are permission-checked and audited.

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

Status: done.

- Fix assignment operators in authorization policies. Done.
- Add cross-school authorization tests for every resource. Done in
  `tests/Feature/CrossSchoolAccessTest.php`.
- Repair the test database configuration. Done. The suite runs in Sail.
- Repair the Larastan configuration. Done. `phpstan-baseline.neon` holds the
  legacy typing backlog, so new errors fail the build.
- Run the full test suite in Sail. Done. 442 tests pass.
- Run Pint, Larastan, and `composer audit` in CI. Done in
  `.github/workflows/laravel-tests.yml`.

Exit condition: security tests pass and the full test suite runs from a clean environment.

### Phase 1: Confirm the business model

- Review each feature section with a school administrator and a teacher.
- Write the valid states and transitions for each workflow.
- Mark required behavior, optional behavior, and future behavior.
- Decide which history must remain immutable.

Exit condition: the cross-feature decisions have written answers.

### Phase 2: Establish domain boundaries

Status: in progress.

- Add a current school context abstraction. Done. `App\Services\School\SchoolContext`
  holds the school for the request, with the `school_context()`, `current_school()`,
  and `current_school_id()` helpers.
- Move school scoping into consistent query or policy boundaries. Done. Models
  with a `school_id` use the `App\Traits\InSchool` trait and are read with
  `Model::inSchool()`. Policies compare the record against the working school.
- Replace magic role strings with named permissions or enums where useful. Done
  for names: `App\Enums\Role` holds the built-in profiles. Moving the remaining
  role checks to permissions is still open.
- Extract high-risk operations into typed actions. Partly done. Account
  provisioning, invitations, account status, and school membership are actions.
  Enrollment, fees, and results still live in services.
- Add database constraints and indexes. Done for schools, memberships,
  invitations, class groups, classes, sections, subjects, and student records.

Exit condition: a new feature cannot read or write another school by missing one local `where` clause.

### Phase 3: Modernize vertical features

Work in this order unless business review changes it:

1. School context and user access.
2. Admissions and student enrollment.
3. Academic years, terms, classes, and sections.
4. Subjects and teacher assignments.
5. Timetables.
6. Exams and results.
7. Fees, invoices, and payments.
8. Notices and reports.

For each feature:

1. Define the user stories.
2. Define the states and invariants.
3. Add or update the data model.
4. Add an application action.
5. Add authorization and validation tests.
6. Update the existing Livewire screen.
7. Add the new UI only after the workflow is stable.

Exit condition: each feature has documented rules, tests, and a working screen.

### Phase 4: Improve the user interface

- Keep Blade and Livewire during domain changes.
- Adopt April UI as the component and visual language for the application. April UI is Blade, Livewire, Alpine, and Tailwind based, so it fits the current rendering stack without a Vue/Inertia rewrite.
- Integrate the active April UI development branch while Laravel 13 support is being finalized. Validate the branch in this application instead of waiting for a tagged package release.
- Resolve the Tailwind version contract between the active April UI branch and this application before migrating screens.
- Keep a small compatibility layer around shared primitives (`button`, `input`, `select`, `modal`, `alert`, table, and layout components) so existing Livewire views can migrate incrementally.
- Identify the screens with the most interaction and slowest workflows.
- Replace the dashboard, authentication, navigation, forms, tables, and feedback states first.
- Migrate feature screens by vertical slice, starting with one complete workflow before changing every view.
- Share authorization and validation with the server; UI components must not contain business rules.

Exit condition: each migrated screen has feature tests and no duplicate business rules in JavaScript.

### Frontend direction

Agreed decision:

- The first frontend modernization uses April UI with Blade, Livewire, Alpine, and Tailwind. A Vue/Inertia rewrite is no longer the default plan.
- April UI components replace the current ad-hoc Blade primitives while Livewire actions, validation, policies, and domain services remain intact.
- UI replacement is incremental and reversible. Existing component names can be retained as wrappers while their markup changes to April UI.
- The active April UI development branch and Tailwind version are explicit integration inputs. Compatibility is tested in this application and is not hidden behind Composer workarounds.

### Phase 5: Operate the product

- Add queues for email and large reports.
- Add scheduled tasks for reminders and maintenance.
- Add audit logs and backup checks.
- Add deployment documentation and CI gates.
- Monitor slow queries and failed jobs.

Exit condition: the system has repeatable deployment, backup, restore, and monitoring procedures.

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
