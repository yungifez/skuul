<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateAcademicCycleSection
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Change the setup of one cycle section.
     *
     * The cycle and the academic level stay as they are. A section is created
     * for one exact cycle, so moving it to another cycle is a roll-forward,
     * not an edit.
     *
     * @param array{name?: string, label?: string|null, stream?: string|null, shift?: string|null, language?: string|null, room?: string|null, capacity?: int|null, position?: int|null} $details
     *
     * @throws InvalidValueException when the section or the records do not allow the change
     */
    public function update(
        AcademicCycleSection $section,
        array $details = [],
        ?User $homeroomTeacher = null,
        ?User $actor = null,
    ): AcademicCycleSection {
        return DB::transaction(function () use ($section, $details, $homeroomTeacher, $actor): AcademicCycleSection {
            /** @var AcademicCycleSection $section */
            $section = AcademicCycleSection::query()
                ->with(['academicYear', 'academicLevel'])
                ->lockForUpdate()
                ->findOrFail($section->id);

            $this->failIfRecordsDoNotFit($section, $homeroomTeacher);

            $before = $this->readable($section);

            $section->fill([
                'name'                => $details['name'] ?? $section->name,
                'label'               => $details['label'] ?? null,
                'stream'              => $details['stream'] ?? null,
                'shift'               => $details['shift'] ?? null,
                'language'            => $details['language'] ?? null,
                'room'                => $details['room'] ?? null,
                'capacity'            => $details['capacity'] ?? null,
                'position'            => $details['position'] ?? 0,
                'homeroom_teacher_id' => $homeroomTeacher?->id,
            ]);

            $after = $this->readable($section);

            if ($before === $after) {
                return $section;
            }

            $section->save();

            $this->auditor->record(
                AuditAction::AcademicCycleSectionUpdated,
                $section,
                ['from' => $before, 'to' => $after],
                $actor,
            );

            return $section;
        });
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(
        AcademicCycleSection $section,
        ?User $homeroomTeacher,
    ): void {
        if ($section->status === AcademicStructureStatus::Archived) {
            throw new InvalidValueException('An archived cycle section cannot be edited.');
        }

        if ($section->academicYear->isClosed()) {
            throw new InvalidValueException('The academic cycle is closed. Reopen it before editing a section.');
        }

        if ($homeroomTeacher !== null) {
            if (!$homeroomTeacher->belongsToSchool($section->school_id)) {
                throw new InvalidValueException('The homeroom teacher does not work in this school.');
            }

            if (!$homeroomTeacher->hasRole(Role::Teacher->value)) {
                throw new InvalidValueException('Only a teacher can be a homeroom teacher.');
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function readable(AcademicCycleSection $section): array
    {
        return [
            'name'                => $section->name,
            'label'               => $section->label,
            'stream'              => $section->stream,
            'shift'               => $section->shift,
            'language'            => $section->language,
            'room'                => $section->room,
            'capacity'            => $section->capacity,
            'position'            => $section->position,
            'homeroom_teacher_id' => $section->homeroom_teacher_id,
        ];
    }
}
