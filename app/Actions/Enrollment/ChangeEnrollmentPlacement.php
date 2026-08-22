<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\EnrollmentPlacement;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Put one enrollment in a class and section, and keep the earlier ones.
 *
 * Admission, promotion, and a section change are all the same event: the
 * student sits somewhere new from a date. Each one is written to an
 * append-only history, so the school can answer where a student sat in any
 * academic year. Repeating the same placement changes nothing and adds no
 * second record, so a retry is safe.
 */
class ChangeEnrollmentPlacement
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Place the enrollment in the given class and section.
     *
     * The cycle section is optional during the compatibility period. When it
     * is given, it becomes the exact source of truth for this new placement
     * and must match the legacy class and section bridges.
     *
     * @throws InvalidValueException when the class, section, cycle section, or year does not fit
     */
    public function place(
        StudentRecord $enrollment,
        MyClass $class,
        ?Section $section = null,
        ?AcademicYear $academicYear = null,
        ?AcademicPeriod $academicPeriod = null,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
        ?AcademicCycleSection $academicCycleSection = null,
    ): StudentRecord {
        $academicYear ??= current_academic_year();

        if ($academicYear === null) {
            throw new InvalidValueException('Set the academic year before you place a student.');
        }

        $enrollmentId = $enrollment->getKey();

        $academicCycleSectionId = $academicCycleSection?->getKey();

        return DB::transaction(function () use ($enrollmentId, $class, $section, $academicYear, $academicPeriod, $actor, $reason, $effectiveOn, $academicCycleSectionId): StudentRecord {
            // Serialize placement changes for one enrollment. This prevents
            // two retries from creating duplicate history rows.
            $enrollment = StudentRecord::query()
                ->lockForUpdate()
                ->findOrFail($enrollmentId);
            $academicCycleSection = $academicCycleSectionId === null
                ? null
                : AcademicCycleSection::query()
                    ->with('academicLevel')
                    ->lockForUpdate()
                    ->findOrFail($academicCycleSectionId);

            $this->failIfRecordsDoNotFit($enrollment, $class, $section, $academicYear, $academicCycleSection);

            // The same place in the same year is not a move. Record nothing.
            if ($this->alreadyPlaced($enrollment, $class, $section, $academicYear, $academicCycleSection)) {
                return $enrollment;
            }

            EnrollmentPlacement::create([
                'student_record_id' => $enrollment->id,
                'academic_year_id' => $academicYear->id,
                'academic_period_id' => $academicPeriod?->id,
                'my_class_id' => $class->id,
                'section_id' => $section?->id,
                'academic_cycle_section_id' => $academicCycleSection?->id,
                'effective_on' => $effectiveOn ?? now(),
                'changed_by' => $actor?->id,
                'reason' => $reason,
            ]);

            // The enrollment keeps a pointer to where the student sits now.
            $enrollment->my_class_id = $class->id;
            $enrollment->section_id = $section?->id;
            $enrollment->academic_cycle_section_id = $academicCycleSection?->id;
            $enrollment->save();

            $enrollment->academicYears()->syncWithoutDetaching([$academicYear->id => [
                'my_class_id' => $class->id,
                'section_id' => $section?->id,
                'academic_cycle_section_id' => $academicCycleSection?->id,
            ]]);

            $this->auditor->record(
                AuditAction::EnrollmentPlaced,
                $enrollment,
                [
                    'academic_year_id' => $academicYear->id,
                    'my_class_id' => $class->id,
                    'section_id' => $section?->id,
                    'academic_cycle_section_id' => $academicCycleSection?->id,
                    'reason' => $reason,
                ],
                $actor,
            );

            return $enrollment;
        });
    }

    /**
     * Check that the class, the section, and the year belong together.
     *
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(
        StudentRecord $enrollment,
        MyClass $class,
        ?Section $section,
        AcademicYear $academicYear,
        ?AcademicCycleSection $academicCycleSection,
    ): void {
        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed. It cannot take a new class.');
        }

        $schoolId = $enrollment->school_id ?? $class->classGroup?->school_id;

        if ($class->classGroup?->school_id !== $schoolId) {
            throw new InvalidValueException('The class belongs to another school.');
        }

        if ($academicYear->school_id !== $schoolId) {
            throw new InvalidValueException('The academic year belongs to another school.');
        }

        if ($section !== null && $section->my_class_id !== $class->id) {
            throw new InvalidValueException('The section is not in the class.');
        }

        if ($academicYear->isClosed()) {
            throw new InvalidValueException('The academic year is closed. Reopen it before you move a student.');
        }

        if ($academicCycleSection === null) {
            return;
        }

        if ($academicCycleSection->school_id !== $schoolId || $academicCycleSection->academic_year_id !== $academicYear->id) {
            throw new InvalidValueException('The cycle section does not belong to this enrollment and academic cycle.');
        }

        if ($academicCycleSection->status !== AcademicStructureStatus::Active) {
            throw new InvalidValueException('Activate the cycle section before placing a student in it.');
        }

        $academicLevel = $academicCycleSection->academicLevel;

        if ($academicLevel->legacy_my_class_id !== $class->id) {
            throw new InvalidValueException('The cycle section does not match the legacy class bridge.');
        }

        if ($academicCycleSection->legacy_section_id === null || $section?->id !== $academicCycleSection->legacy_section_id) {
            throw new InvalidValueException('The cycle section does not match the legacy section bridge.');
        }
    }

    /**
     * Check if the enrollment already sits here in this year.
     */
    private function alreadyPlaced(
        StudentRecord $enrollment,
        MyClass $class,
        ?Section $section,
        AcademicYear $academicYear,
        ?AcademicCycleSection $academicCycleSection,
    ): bool {
        return $enrollment->placements()
            ->where('academic_year_id', $academicYear->id)
            ->where('my_class_id', $class->id)
            ->where('section_id', $section?->id)
            ->when(
                $academicCycleSection !== null,
                fn ($query) => $query->where('academic_cycle_section_id', $academicCycleSection->id),
            )
            ->exists();
    }
}
