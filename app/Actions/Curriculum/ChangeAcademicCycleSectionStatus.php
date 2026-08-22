<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChangeAcademicCycleSectionStatus
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * @throws InvalidValueException when the transition is not allowed
     */
    public function change(
        AcademicCycleSection $section,
        AcademicStructureStatus $status,
        ?User $actor = null,
    ): AcademicCycleSection {
        return DB::transaction(function () use ($section, $status, $actor): AcademicCycleSection {
            $section = AcademicCycleSection::query()
                ->with('academicYear')
                ->lockForUpdate()
                ->findOrFail($section->id);

            if ($section->status === $status) {
                return $section;
            }

            $from = $section->status;

            if (!$from->canMoveTo($status)) {
                throw new InvalidValueException("A {$from->label()} cycle section cannot become {$status->label()}.");
            }

            if ($status === AcademicStructureStatus::Active && $section->academicYear->isClosed()) {
                throw new InvalidValueException('The academic cycle is closed. Reopen it before activating a section.');
            }

            $section->status = $status;
            $section->save();

            $this->auditor->record(
                AuditAction::AcademicCycleSectionStatusChanged,
                $section,
                [
                    'from' => $from->value,
                    'to'   => $status->value,
                ],
                $actor,
            );

            return $section;
        });
    }
}
