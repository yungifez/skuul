---
paths:
  - 'app/Services/Syllabus/**'
  - 'app/Actions/Syllabus/**'
---

# Syllabus

## Syllabus revisions share one stored file
`ReviseSyllabus` copies the parent's `file` path into the new draft, so one PDF on the public disk can back several `syllabi` rows. Never call `Storage::delete($syllabus->file)` directly. Use `SyllabusService::deleteFileIfUnused()`, which deletes only when no row still names the path. Only a draft is editable; a published revision changes by revise → edit → publish, and `PublishSyllabus` refuses a draft whose parent is no longer the published one.

## Records that point at a topic move with each revision
Each revision has its own `syllabus_topics` rows. A revision copies the topics and sets `copied_from_id`. When it is published, `PublishSyllabus::carryCoverageForward()` moves the rows that point at the old topics onto the new ones. Today these rows are `syllabus_topic_coverages`, `lesson_notes`, and `grade_item_syllabus_topic`. A new table that names a `syllabus_topic_id` must be added there too. Otherwise its rows stay on the superseded revision, and the published plan loses them.
