---
paths:
  - 'tests/**'
---

# Tests

## School context and roles in tests
Tests start inside School::first() (see Tests\TestCase::setUp). Roles and permissions are held per school, so call `school_context()->set($school, remember: false)` before `assignRole()` or `givePermissionTo()`. Use App\Traits\FeatureTestTrait: `authorized_user([...])`, `memberOf()`, `nonMember()`, `platform_admin()`. tests/Feature/CrossSchoolAccessTest.php builds a full second school; add new resources to its data provider so cross-school access stays covered.
