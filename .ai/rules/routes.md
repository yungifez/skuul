---
paths:
  - 'routes/*.php'
---

# Routes

## Register literal sub-paths before the resource that owns the prefix
`Route::resource('academic-cycle-sections', ...)` claims
`GET academic-cycle-sections/{academicCycleSection}`, so a later
`GET academic-cycle-sections/roll-forward` is read as a model key and 404s.
Declare the literal path above the `Route::resource` call. Laravel already
orders `create` before `show` inside a resource; only routes you add yourself
need this.
