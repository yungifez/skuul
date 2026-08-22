<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChangeAcademicLevelStatus
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Move a reusable level between lifecycle states.
     *
     * Archiving stops the level from being offered for a new cycle section.
     * It keeps every past section, placement, and result that names it.
     *
     * @throws InvalidValueException when the transition is not allowed
     */
    public function change(
        AcademicLevel $academicLevel,
        AcademicStructureStatus $status,
        ?User $actor = null,
    ): AcademicLevel {
        return DB::transaction(function () use ($academicLevel, $status, $actor): AcademicLevel {
            /** @var AcademicLevel $academicLevel */
            $academicLevel = AcademicLevel::query()->lockForUpdate()->findOrFail($academicLevel->id);

            if ($academicLevel->status === $status) {
                return $academicLevel;
            }

            $from = $academicLevel->status;

            if (!$from->canMoveTo($status)) {
                throw new InvalidValueException("A {$from->label()} academic level cannot become {$status->label()}.");
            }

            if ($status === AcademicStructureStatus::Archived) {
                $this->failIfSectionsStillRun($academicLevel);
            }

            $academicLevel->status = $status;
            $academicLevel->save();

            $this->auditor->record(
                AuditAction::AcademicLevelStatusChanged,
                $academicLevel,
                ['from' => $from->value, 'to' => $status->value],
                $actor,
            );

            return $academicLevel;
        });
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfSectionsStillRun(AcademicLevel $academicLevel): void
    {
        $running = AcademicCycleSection::query()
            ->where('academic_level_id', $academicLevel->id)
            ->whereIn('status', [AcademicStructureStatus::Draft, AcademicStructureStatus::Active])
            ->count();

        if ($running > 0) {
            throw new InvalidValueException("Archive the {$running} draft or active cycle sections of this level first.");
        }
    }
}
