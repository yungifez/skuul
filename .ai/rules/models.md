---
paths:
  - 'app/Models/*.php'
---

# Models

## Scope school-owned models with inSchool()
Every model with a `school_id` column uses the `App\Traits\InSchool` trait. Query them with `Model::inSchool()`, never `where('school_id', current_school_id())`. The scope is the one place that turns "the school I am working in" into a query condition, so a missed local where clause cannot leak another school's records. Datatable views pass it as a filter: `['name' => 'inSchool']`. Models owned through a relation (Fee, Section, Exam, ...) stay scoped through their parent.
