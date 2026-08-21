---
paths:
  - 'app/Models/*.php'
---

# Models

## Scope school-owned models with inSchool()
Every model with a `school_id` column uses the `App\Traits\InSchool` trait. Query them with `Model::inSchool()`, never `where('school_id', current_school_id())`. The scope is the one place that turns "the school I am working in" into a query condition, so a missed local where clause cannot leak another school's records. Datatable views pass it as a filter: `['name' => 'inSchool']`. Models owned through a relation (Fee, Section, Exam, ...) stay scoped through their parent. The trait also fills `school_id` on create from the working school, so a write cannot forget it; code that names a school keeps it. `tests/Feature/SchoolScopeTest.php` fails when a new school-owned model misses the trait.

## Enrollment state lives in one place
`student_records` is the student enrollment. Its state is the `status` column,
cast to `App\Enums\EnrollmentStatus`; the old `is_graduated` boolean and its
global scope are gone. Change a state only through
`App\Actions\Enrollment\ChangeEnrollmentStatus`, which checks the transition,
skips a repeated request, and writes an `enrollment_status_changes` row with
the actor, reason, and effective date. That history is append-only: the model
refuses updates and deletes.

Listings of people who attend now must say so, with `User::activeStudents()`
or the `activeStudents` datatable filter. Nothing hides graduated students
automatically any more.

## One person can hold several enrollments
`student_records.school_id` names the school of the enrollment, so a person
can attend two schools at once. `User::studentRecords()` returns all of them.
`User::studentRecord()` returns one: the primary enrollment of the school
being worked in. Never use it to prove a person has only one enrollment.

## Placement is a history, not a field
`student_records.my_class_id` and `section_id` are pointers to the newest
placement. The history lives in `enrollment_placements`, which is append-only.
Change a placement only through
`App\Actions\Enrollment\ChangeEnrollmentPlacement`; it checks the class, the
section, the school, and the period, then writes the next record. Move a
student between schools with `App\Actions\Enrollment\TransferEnrollment`.
