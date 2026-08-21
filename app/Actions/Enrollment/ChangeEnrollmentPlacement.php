<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\EnrollmentPlacement;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\Semester;
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
     * @throws InvalidValueException when the class, section, or year does not fit
     */
    public function place(
        StudentRecord $enrollment,
        MyClass $class,
        ?Section $section = null,
        ?AcademicYear $academicYear = null,
        ?Semester $semester = null,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
    ): StudentRecord {
        $academicYear ??= current_academic_year();

        if ($academicYear === null) {
            throw new InvalidValueException('Set the academic year before you place a student.');
        }

        $enrollmentId = $enrollment->getKey();

        return DB::transaction(function () use ($enrollmentId, $class, $section, $academicYear, $semester, $actor, $reason, $effectiveOn): StudentRecord {
            // Serialize placement changes for one enrollment. This prevents
            // two retries from creating duplicate history rows.
            $enrollment = StudentRecord::query()
                ->lockForUpdate()
                ->findOrFail($enrollmentId);

            $this->failIfRecordsDoNotFit($enrollment, $class, $section, $academicYear);

            // The same place in the same year is not a move. Record nothing.
            if ($this->alreadyPlaced($enrollment, $class, $section, $academicYear)) {
                return $enrollment;
            }

            EnrollmentPlacement::create([
                'student_record_id' => $enrollment->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester?->id,
                'my_class_id' => $class->id,
                'section_id' => $section?->id,
                'effective_on' => $effectiveOn ?? now(),
                'changed_by' => $actor?->id,
                'reason' => $reason,
            ]);

            // The enrollment keeps a pointer to where the student sits now.
            $enrollment->my_class_id = $class->id;
            $enrollment->section_id = $section?->id;
            $enrollment->save();

            $enrollment->academicYears()->syncWithoutDetaching([$academicYear->id => [
                'my_class_id' => $class->id,
                'section_id' => $section?->id,
            ]]);

            $this->auditor->record(
                AuditAction::EnrollmentPlaced,
                $enrollment,
                [
                    'academic_year_id' => $academicYear->id,
                    'my_class_id' => $class->id,
                    'section_id' => $section?->id,
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
    private function failIfRecordsDoNotFit(StudentRecord $enrollment, MyClass $class, ?Section $section, AcademicYear $academicYear): void
    {
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
    }

    /**
     * Check if the enrollment already sits here in this year.
     */
    private function alreadyPlaced(StudentRecord $enrollment, MyClass $class, ?Section $section, AcademicYear $academicYear): bool
    {
        return $enrollment->placements()
            ->where('academic_year_id', $academicYear->id)
            ->where('my_class_id', $class->id)
            ->where('section_id', $section?->id)
            ->exists();
    }
}
