---
paths:
  - 'app/Imports/**,tests/Feature/ImportTest.php'
---

# Feature

## Use canonical gender values in import rows
Use the application gender values exactly: Male, Female, Non-binary, or Prefer not to say. Import validation passes these values to account provisioning, which uses the same case-sensitive enum list.
