---
paths:
  - 'app/Actions/Enrollment/**'
  - 'app/Services/Authorization/CampusMoveAuthority.php'
---

# Enrollment

## A campus move is not a transfer
A campus is a school inside an organization. Two schools that share an
`organization_id` are two campuses of one school group.

Moving a student between them is an internal move: use
`MoveEnrollmentBetweenCampuses`. It keeps one enrollment, the admission
number, and the placement history, and only appends the next
`enrollment_placements` row. `TransferEnrollment` is for crossing
organizations only; it throws when both schools share one, closes the old
enrollment, and opens a new one linked by `transferred_from_id`.

Both actions grant a `school_memberships` row at the destination.
`User::scopeOfSchool()` reads that membership, so a student without one is
invisible at the school they attend. A campus move deliberately keeps the
source membership active so the old campus still reads the records made
there; the student still leaves its student lists, because
`User::activeStudents()` resolves the enrollment through the working school.

## Who may move a student is tiered
Never call `MoveEnrollmentBetweenCampuses` straight from a screen. Ask
`App\Services\Authorization\CampusMoveAuthority` first:

- `movesFreely()` — the person holds `OrganizationPermission::MoveStudents`
  in that organization. Move now.
- otherwise `canRequest()` — the school permission `request campus move`.
  Write a `CampusMoveRequest` through `RequestCampusMove` instead.

The receiving campus decides with `approve campus move`; a person with
organization authority may decide as well. `approve()` performs the move in
the same transaction, so there is no approved-but-unapplied state. One
student may hold only one open request, and a decided request cannot be
decided again.

## The campus move screen
`campus-moves.index` is the receiving campus's queue. It reads only requests
whose `to_school_id` or `from_school_id` is the working campus, so it needs no
school scope of its own. `CampusMoveRequestPolicy` guards it; the sidebar
entry asks `viewAny`, so anybody who can neither ask nor decide never sees it.

## Checking a permission against another campus
School permissions are held per school through Spatie's team scope. A check
against a campus other than the working one must switch the team id AND unset
and restore the user's `roles` and `permissions` relations. Without that the
loaded relations answer for the school the person is working in, and the
check silently passes. `CampusMoveAuthority::withinSchool()` already does
this; copy it rather than calling `hasPermissionTo()` directly.

Never run that switch for the working school itself. Its roles and permissions
are already loaded, and reloading them makes every screen repeat the
permission queries; `PlatformPermissionTest` fails when the dashboard loads
them more than once. `allowsInSchool()` short-circuits on
`current_school_id()` and memoises the rest.
