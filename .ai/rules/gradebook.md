---
paths:
  - 'app/Actions/Gradebook/**'
---

# Gradebook

## Grade scales are controlled option sets
A scale-based grade item always points to a school-owned GradingScale. Grade entries store a grading_scale_option_id, never a free-text label; the option supplies any configured points. An option referenced by a learner record is immutable and cannot be removed.
