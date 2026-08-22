<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\EnrollmentPlacement;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Put one enrollment in an exact cycle section, and keep the earlier ones.
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
     * Place the enrollment in an exact academic-cycle section.
     *
     * @throws InvalidValueException when the cycle section, period, or enrollment does not fit
     */
    public function place(
        StudentRecord $enrollment,
        AcademicCycleSection $academicCycleSection,
        ?AcademicPeriod $academicPeriod = null,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
    ): StudentRecord {
        $enrollmentId = $enrollment->getKey();

        return DB::transaction(function () use ($enrollmentId, $academicCycleSection, $academicPeriod, $actor, $reason, $effectiveOn): StudentRecord {
            // Serialize placement changes for one enrollment. This prevents
            // two retries from creating duplicate history rows.
            $enrollment = StudentRecord::query()
                ->lockForUpdate()
                ->findOrFail($enrollmentId);
            $academicCycleSection = AcademicCycleSection::query()
                ->with('academicYear')
                ->lockForUpdate()
                ->findOrFail($academicCycleSection->getKey());
            $academicYear = $academicCycleSection->academicYear;

            $this->failIfRecordsDoNotFit($enrollment, $academicCycleSection, $academicPeriod);

            // The same place in the same year is not a move. Record nothing.
            if ($this->alreadyPlaced($enrollment, $academicCycleSection)) {
                return $enrollment;
            }

            EnrollmentPlacement::create([
                'student_record_id' => $enrollment->id,
                'academic_year_id' => $academicYear->id,
                'academic_period_id' => $academicPeriod?->id,
                'academic_cycle_section_id' => $academicCycleSection->id,
                'effective_on' => $effectiveOn ?? now(),
                'changed_by' => $actor?->id,
                'reason' => $reason,
            ]);

            // The enrollment keeps a pointer to where the student sits now.
            $enrollment->academic_cycle_section_id = $academicCycleSection->id;
            $enrollment->save();

            $enrollment->academicYears()->syncWithoutDetaching([$academicYear->id => [
                'academic_cycle_section_id' => $academicCycleSection->id,
            ]]);

            $this->auditor->record(
                AuditAction::EnrollmentPlaced,
                $enrollment,
                [
                    'academic_year_id' => $academicYear->id,
                    'academic_cycle_section_id' => $academicCycleSection->id,
                    'reason' => $reason,
                ],
                $actor,
            );

            return $enrollment;
        });
    }

    /**
     * Check that the cycle section and period belong to this enrollment.
     *
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(
        StudentRecord $enrollment,
        AcademicCycleSection $academicCycleSection,
        ?AcademicPeriod $academicPeriod,
    ): void {
        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed. It cannot take a new placement.');
        }

        if ($academicCycleSection->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('The cycle section belongs to another school.');
        }

        if ($academicCycleSection->academicYear->isClosed()) {
            throw new InvalidValueException('The academic year is closed. Reopen it before you move a student.');
        }

        if ($academicCycleSection->status !== AcademicStructureStatus::Active) {
            throw new InvalidValueException('Activate the cycle section before placing a student in it.');
        }

        if ($academicPeriod !== null && $academicPeriod->academic_year_id !== $academicCycleSection->academic_year_id) {
            throw new InvalidValueException('The academic period does not belong to the cycle section’s academic year.');
        }
    }

    /**
     * Check if the enrollment already sits here in this year.
     */
    private function alreadyPlaced(
        StudentRecord $enrollment,
        AcademicCycleSection $academicCycleSection,
    ): bool {
        return $enrollment->academic_cycle_section_id === $academicCycleSection->id;
    }
}
