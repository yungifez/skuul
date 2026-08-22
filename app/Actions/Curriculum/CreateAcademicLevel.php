<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateAcademicLevel
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @throws InvalidValueException when the parent belongs to another school
     */
    public function create(
        string $name,
        ?string $label = null,
        ?string $code = null,
        ?AcademicLevel $parent = null,
        int $position = 0,
        ?User $actor = null,
    ): AcademicLevel {
        $schoolId = current_school_id();

        if ($parent !== null && $parent->school_id !== $schoolId) {
            throw new InvalidValueException('The parent academic level belongs to another school.');
        }

        return DB::transaction(function () use ($schoolId, $name, $label, $code, $parent, $position, $actor): AcademicLevel {
            $academicLevel = AcademicLevel::create([
                'school_id' => $schoolId,
                'parent_id' => $parent?->id,
                'name' => $name,
                'label' => $label,
                'code' => $code,
                'position' => $position,
                'status' => AcademicStructureStatus::Active,
            ]);

            $this->auditor->record(
                AuditAction::AcademicLevelCreated,
                $academicLevel,
                [
                    'parent_id' => $parent?->id,
                    'code' => $code,
                ],
                $actor,
            );

            return $academicLevel;
        });
    }
}
