<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\CourseOfferingStatus;
use App\Exceptions\InvalidValueException;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Give a course offering a name of its own, or take it away.
 *
 * A school names an offering when it runs two of one subject for one level,
 * such as Algebra A and Algebra B, so staff and learners can tell them apart.
 */
class RenameCourseOffering
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function rename(CourseOffering $courseOffering, ?string $name, ?User $actor = null): CourseOffering
    {
        $name = trim((string) $name);
        $name = $name === '' ? null : $name;

        if ($name !== null && mb_strlen($name) > 100) {
            throw new InvalidValueException('Use a name of 100 characters or fewer.');
        }

        return DB::transaction(function () use ($courseOffering, $name, $actor): CourseOffering {
            $courseOffering = CourseOffering::query()->lockForUpdate()->findOrFail($courseOffering->id);

            if ($courseOffering->status === CourseOfferingStatus::Archived) {
                throw new InvalidValueException('An archived course offering cannot be changed.');
            }

            if ($courseOffering->name === $name) {
                return $courseOffering;
            }

            $previous = $courseOffering->name;
            $courseOffering->name = $name;
            $courseOffering->save();

            $this->auditor->record(AuditAction::CourseOfferingRenamed, $courseOffering, ['from' => $previous, 'to' => $name], $actor);

            return $courseOffering;
        });
    }
}
