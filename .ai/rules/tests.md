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

## Sign in once per test, not once per request

`authorized_user()` calls `actingAs(...)->withSession(...)`. A second call in
the same test replaces the session that the first HTTP request wrote, so every
request after it arrives as a guest and is redirected to `/login`. A test that
only asserts a redirect then passes for the wrong reason.

Take the return value once, name it, and send every request through it:

```php
$office = $this->authorized_user(['read fee invoice', 'delete fee invoice record']);
$office->post(...)->assertRedirect();
$office->delete(...)->assertSessionHas('danger');
```

## Pest runs the suite, but test impact analysis cannot

`pestphp/pest` is installed and `artisan test` runs through it. Every test in
this project is a PHPUnit class, which Pest runs without complaint.

Do not turn on test impact analysis. `pest()->tia()->locally()` in
`tests/Pest.php` stops the whole suite on the first file it reads:

```
ERROR  Tia mode requires Pest tests.
Encountered PHPUnit class Tests\Unit\BreadcrumbLabelTest
```

A single file still runs, so the fault only shows on a full run.

## An isolated test needs PHPUNIT_COMPOSER_INSTALL

A test marked `#[RunInSeparateProcess]` runs in a child process PHPUnit builds
from a template. The template loads the autoloader from
`PHPUNIT_COMPOSER_INSTALL`, and loads nothing when that constant is missing, so
the child dies with `Class "PHPUnit\TextUI\Configuration\Registry" not found`.
The phpunit binary defines the constant; the pest binary does not, so
`tests/bootstrap.php` defines it.

`tests/bootstrap.php` also holds the database lock. The child process must not
take that lock, so its parent-process check names both binaries.
