<?php

namespace App\Actions\Calendar;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\CalendarTemplate;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Point a campus at a calendar of its own, or return it to its organization's.
 *
 * A campus that keeps its own calendar stops lining up with the rest of the
 * organization, so a report that compares campuses compares different weeks.
 * That is sometimes right and never accidental, which is why it is an action
 * with a reason and an audit event rather than a column a form writes.
 */
class SetCampusCalendarTemplate
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Give the campus its own template.
     *
     * @throws InvalidValueException when the template belongs elsewhere
     */
    public function override(School $school, CalendarTemplate $template, ?User $actor = null, ?string $reason = null): School
    {
        if ($template->organization_id !== $school->organization_id) {
            throw new InvalidValueException('That calendar template belongs to another organization.');
        }

        if (trim((string) $reason) === '') {
            throw new InvalidValueException('Say why this campus needs a calendar of its own.');
        }

        return $this->apply($school, $template->id, $actor, $reason);
    }

    /**
     * Return the campus to its organization's default calendar.
     */
    public function inherit(School $school, ?User $actor = null, ?string $reason = null): School
    {
        return $this->apply($school, null, $actor, $reason);
    }

    /**
     * Write the choice and record it.
     */
    private function apply(School $school, ?int $templateId, ?User $actor, ?string $reason): School
    {
        $before = $school->calendar_template_id;

        if ($before === $templateId) {
            return $school;
        }

        return DB::transaction(function () use ($school, $templateId, $before, $actor, $reason): School {
            $school->calendar_template_id = $templateId;
            $school->save();

            $this->auditor->record(
                AuditAction::CampusCalendarOverridden,
                $school,
                [
                    'from'     => $before,
                    'to'       => $templateId,
                    'inherits' => $templateId === null,
                    'reason'   => $reason,
                ],
                $actor,
            );

            return $school;
        });
    }
}
