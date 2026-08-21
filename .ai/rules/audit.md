---
paths:
  - 'app/Actions/**'
  - 'app/Listeners/**'
  - 'app/Services/**'
---

# Audit log

Sensitive actions are recorded through `App\Actions\Audit\RecordAuditEvent`.
Call `record(AuditAction $action, ?Model $subject, array $context, ?User $actor)`
from the action or service that makes the change, inside the same transaction.
The actor is the signed-in user by default, and the school comes from the
subject or the working school, so a caller only names them when they differ.

Add a case to `App\Enums\AuditAction` for each new action. Use it; never write
a raw string. Access changes need no call at all: `config/permission.php` has
`events_enabled` set to true, and `App\Listeners\RecordPermissionChanges`
listens to the four Spatie role and permission events. Leave that flag on.

`audit_events` is append-only. The model throws on update and delete, so
correcting the history means recording the next action.
