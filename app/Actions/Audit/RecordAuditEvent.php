<?php

namespace App\Actions\Audit;

use App\Enums\AuditAction;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Write one sensitive action to the audit log.
 *
 * The log answers "who did this, to what, when, and from where". It never
 * blocks the work it describes: the caller keeps its own transaction, and a
 * missing actor or school only makes the record less specific.
 */
class RecordAuditEvent
{
    /**
     * Record the action.
     *
     * @param Model|null           $subject the record the action was made on
     * @param array<string, mixed> $context extra facts worth keeping
     * @param User|null            $actor   the person who acted; the signed-in user by default
     * @param School|int|null      $school  the school the action belongs to
     */
    public function record(
        AuditAction $action,
        ?Model $subject = null,
        array $context = [],
        ?User $actor = null,
        School|int|null $school = null,
    ): AuditEvent {
        $actor ??= auth()->user();
        $schoolId = $school instanceof School ? $school->id : $school;

        return AuditEvent::create([
            'school_id'    => $schoolId ?? $this->schoolOf($subject) ?? current_school_id(),
            'actor_id'     => $actor?->id,
            'action'       => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id'   => $subject?->getKey(),
            'context'      => $context === [] ? null : $context,
            'ip_address'   => request()->ip(),
        ]);
    }

    /**
     * Get the school a subject belongs to, when it names one.
     */
    private function schoolOf(?Model $subject): ?int
    {
        if ($subject === null) {
            return null;
        }

        $schoolId = $subject->getAttribute('school_id');

        return is_int($schoolId) ? $schoolId : null;
    }
}
