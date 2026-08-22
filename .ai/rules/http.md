---
paths:
  - 'app/Http/Controllers/**'
  - 'app/Http/Requests/**'
---

# Controllers and form requests

## Keep a named person inside the school with the membership rule
A field that names a person (`assigned_to`, `staff_id`, `user_id`) must not
accept an account from another school. Use `App\Traits\ValidatesSchoolMembership`
and its `memberOfWorkingSchool()`, which reads `school_memberships` directly.

Do not write `Rule::exists('users', 'id')->whereIn('id', fn ($query) => ...)`.
Larastan fails it: `Exists::whereIn()` is typed for
`array|BackedEnum|Arrayable`, not a Closure.

## The two people lists a screen needs
`App\Traits\ListsSchoolPeople` gives `schoolLearners()` and `schoolStaff()`.
The staff list leaves out anybody enrolled in the working school, because a
learner never handles a case or runs a plan. Use it instead of writing the
query again.

## withCount closures get a plain Builder, so model scopes fail
Inside `withCount(['members as x' => function (Builder $query) ...])` the
closure receives a generic `Illuminate\Database\Eloquent\Builder`, so Larastan
cannot see the related model's scopes and reports
"Call to an undefined method Builder::current()".

Write the scope's condition out in the closure and name the scope in a
comment. `CohortController::index()` and `ProgramController::index()` do this.

## A screen of a feature the school can turn off is gated twice
Put the routes behind the `feature:<name>` middleware, and give the sidebar
entry in `App\Livewire\Layouts\Menu` a `visible` of
`feature_enabled(Feature::X) && $user->can(...)`. The middleware closes the way
in; the menu stops offering a link that would 404. Records are never touched,
so a school can turn the feature back on and find its history.

## The layout already shows session messages
`layouts.app` renders `@livewire('display-status')`, which shows
`session('success')`, `session('danger')`, and `session('info')`. A page that
adds its own success alert says the same thing twice. `ApplicationException`
renders as `back()->with('danger', ...)`, so a caught `InvalidValueException`
needs no alert of its own either. Keep page-level alerts for `$errors`, which
the status display does not carry.
