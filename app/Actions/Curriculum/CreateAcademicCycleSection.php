<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateAcademicCycleSection
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @param  array{label?: string|null, stream?: string|null, shift?: string|null, language?: string|null, room?: string|null, capacity?: int|null, position?: int|null}  $details
     *
     * @throws InvalidValueException when the records do not belong to one school
     */
    public function create(
        AcademicYear $academicYear,
        AcademicLevel $academicLevel,
        string $name,
        array $details = [],
        ?User $homeroomTeacher = null,
        ?User $actor = null,
    ): AcademicCycleSection {
        $this->failIfRecordsDoNotFit($academicYear, $academicLevel, $homeroomTeacher);

        return DB::transaction(function () use ($academicYear, $academicLevel, $name, $details, $homeroomTeacher, $actor): AcademicCycleSection {
            $section = AcademicCycleSection::create([
                'school_id' => $academicYear->school_id,
                'academic_year_id' => $academicYear->id,
                'academic_level_id' => $academicLevel->id,
                'homeroom_teacher_id' => $homeroomTeacher?->id,
                'name' => $name,
                'label' => $details['label'] ?? null,
                'stream' => $details['stream'] ?? null,
                'shift' => $details['shift'] ?? null,
                'language' => $details['language'] ?? null,
                'room' => $details['room'] ?? null,
                'capacity' => $details['capacity'] ?? null,
                'position' => $details['position'] ?? 0,
                'status' => AcademicStructureStatus::Draft,
            ]);

            $this->auditor->record(
                AuditAction::AcademicCycleSectionCreated,
                $section,
                [
                    'academic_year_id' => $academicYear->id,
                    'academic_level_id' => $academicLevel->id,
                    'homeroom_teacher_id' => $homeroomTeacher?->id,
                ],
                $actor,
            );

            return $section;
        });
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(
        AcademicYear $academicYear,
        AcademicLevel $academicLevel,
        ?User $homeroomTeacher,
    ): void {
        if ($academicYear->school_id !== $academicLevel->school_id) {
            throw new InvalidValueException('The academic level belongs to another school.');
        }

        if ($academicYear->isClosed()) {
            throw new InvalidValueException('The academic cycle is closed. Reopen it before configuring sections.');
        }

        if ($homeroomTeacher !== null) {
            if (!$homeroomTeacher->belongsToSchool($academicYear->school_id)) {
                throw new InvalidValueException('The '.strtolower(school_term('homeroom_teacher', 'class teacher')).' does not work in this school.');
            }

            if (!$homeroomTeacher->hasRole(Role::Teacher->value)) {
                throw new InvalidValueException('Only a teacher can be a '.strtolower(school_term('homeroom_teacher', 'class teacher')).'.');
            }
        }
    }
}
