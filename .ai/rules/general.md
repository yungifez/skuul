---
paths:
  - '**'
---

# General

## Quality gates run in CI
.github/workflows/laravel-tests.yml runs Pint, Larastan, `composer audit`, and the test suite. Keep them green: run `vendor/bin/sail bin pint` and `vendor/bin/sail php vendor/bin/phpstan analyse` before finishing. pint.json is the laravel preset with `not_operator_with_successor_space` off (`!$x`, not `! $x`). phpstan-baseline.neon holds ~200 legacy typing errors; fix errors, never extend the baseline. CI checks out yungifez/april-ui beside the app because composer.json requires it from a path repository.
