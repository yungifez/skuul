---
paths:
  - 'tests/**'
---

# Tests

## School context and roles in tests
Tests start inside School::first() (see Tests\TestCase::setUp). Roles and permissions are held per school, so call `school_context()->set($school, remember: false)` before `assignRole()` or `givePermissionTo()`. Use App\Traits\FeatureTestTrait: `authorized_user([...])`, `memberOf()`, `nonMember()`, `platform_admin()`. tests/Feature/CrossSchoolAccessTest.php builds a full second school; add new resources to its data provider so cross-school access stays covered.

## Screen tests: assert on rows, not on names

A filtered list still renders every option in its filter menu, so
`assertDontSee('<name>')` fails on the menu. Assert on the row's show-route URL
or a value only that record carries. See `.ai/rules/views.md`.

## route() takes one array, not a model and then a query string

`route('x.show', $model, ['filter' => 1])` silently drops the filter: the third
argument of `route()` is `$absolute`, not more parameters. The request arrives
with no query string, the screen renders its unfiltered state, and the failure
looks like a broken filter rather than a broken test.

Write `route('x.show', [$model, 'filter' => 1])`.
