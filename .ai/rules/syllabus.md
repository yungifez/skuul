---
paths:
  - 'app/Services/Syllabus/**'
---

# Syllabus

## Syllabus revisions share one stored file
`ReviseSyllabus` copies the parent's `file` path into the new draft, so one PDF on the public disk can back several `syllabi` rows. Never call `Storage::delete($syllabus->file)` directly. Use `SyllabusService::deleteFileIfUnused()`, which deletes only when no row still names the path. Only a draft is editable; a published revision changes by revise → edit → publish, and `PublishSyllabus` refuses a draft whose parent is no longer the published one.
