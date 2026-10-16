<?php

namespace App\Listeners;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Events\AccountStatusChanged;

/**
 * Keep every account state change in the audit log.
 *
 * Locking, suspending, or restoring an account decides who can sign in, so
 * each change records the actor and the reason.
 */
class RecordAccountStatusChange
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Handle the event.
     */
    public function handle(AccountStatusChanged $event): void
    {
        $this->auditor->record(
            AuditAction::AccountStatusChanged,
            $event->user,
            [
                'from' => $event->from->value,
                'to' => $event->to->value,
                'reason' => $event->reason,
            ],
            $event->changedBy,
        );
    }
}
