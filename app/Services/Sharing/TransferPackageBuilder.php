<?php

namespace App\Services\Sharing;

use App\Enums\DataCategory;
use App\Models\DataSharingRequest;
use App\Models\Incident;
use App\Models\ParentRecord;
use App\Models\ResultSnapshot;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Services\Attendance\AttendanceSummary;
use App\Services\Finance\StudentLedger;
use Illuminate\Database\Eloquent\Collection;

/**
 * Build the copy of a student's records that one school hands to another.
 *
 * The package holds exactly the categories that were approved, and nothing
 * else. Every part is labelled with the school it came from, so the receiving
 * school reads a snapshot rather than a record of its own.
 */
class TransferPackageBuilder
{
    public function __construct(
        private AttendanceSummary $attendance,
        private StudentLedger $ledger,
    ) {
    }

    /**
     * Build the payload for one approved request.
     *
     * @return array<string, mixed>
     */
    public function build(DataSharingRequest $request): array
    {
        $enrollment = $request->studentRecord;
        $payload = [
            'source_school_id'  => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'built_at'          => now()->toIso8601String(),
            'purpose'           => $request->purpose,
        ];

        foreach ($request->categories() as $category) {
            $payload[$category->value] = $this->partFor($category, $enrollment);
        }

        return $payload;
    }

    /**
     * Build one category of the package.
     *
     * @return array<string, mixed>|array<int, mixed>
     */
    private function partFor(DataCategory $category, StudentRecord $enrollment): array
    {
        return match ($category) {
            DataCategory::Identity        => $this->identity($enrollment),
            DataCategory::Guardians       => $this->guardians($enrollment),
            DataCategory::Enrollment      => $this->enrollment($enrollment),
            DataCategory::AcademicResults => $this->results($enrollment),
            DataCategory::Attendance      => $this->attendance->forStudent($enrollment),
            DataCategory::Health          => $this->health($enrollment),
            DataCategory::Discipline      => $this->discipline($enrollment),
            DataCategory::Safeguarding    => $this->safeguarding($enrollment),
            DataCategory::Wellbeing       => $this->wellbeing($enrollment),
            DataCategory::Finance         => $this->finance($enrollment),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function identity(StudentRecord $enrollment): array
    {
        $person = $enrollment->user;

        return [
            'name'        => $person?->name,
            'email'       => $person?->email,
            'birthday'    => $person?->birthday,
            'gender'      => $person?->gender,
            'nationality' => $person?->nationality,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function guardians(StudentRecord $enrollment): array
    {
        $person = $enrollment->user;

        if ($person === null) {
            return [];
        }

        return ParentRecord::query()
            ->whereHas('students', fn ($query) => $query->where('users.id', $person->id))
            ->with('user')
            ->get()
            ->map(fn (ParentRecord $parentRecord): array => [
                'name'  => $parentRecord->user?->name,
                'email' => $parentRecord->user?->email,
                'phone' => $parentRecord->user?->phone,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function enrollment(StudentRecord $enrollment): array
    {
        $enrollment->loadMissing([
            'academicCycleSection.academicLevel',
            'placements.academicCycleSection.academicLevel',
        ]);

        $currentCycleSection = $enrollment->academicCycleSection;

        return [
            'admission_number' => $enrollment->admission_number,
            'admission_date'   => $enrollment->admission_date,
            'status'           => $enrollment->status->value,
            'academic_level'   => $currentCycleSection === null ? null : ($currentCycleSection->academicLevel->label ?? $currentCycleSection->academicLevel->name),
            'cycle_section'    => $currentCycleSection === null ? null : ($currentCycleSection->label ?? $currentCycleSection->name),
            'placements'       => $enrollment->placements->map(function ($placement): array {
                $cycleSection = $placement->academicCycleSection;

                return [
                    'academic_cycle_section_id' => $placement->academic_cycle_section_id,
                    'academic_level'            => $cycleSection === null ? null : ($cycleSection->academicLevel->label ?? $cycleSection->academicLevel->name),
                    'cycle_section'             => $cycleSection === null ? null : ($cycleSection->label ?? $cycleSection->name),
                    'effective_on'              => $placement->effective_on,
                    'reason'                    => $placement->reason,
                ];
            })->all(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function results(StudentRecord $enrollment): array
    {
        return ResultSnapshot::query()
            ->approved()
            ->where('student_record_id', $enrollment->id)
            ->with('courseOffering.subject')
            ->get()
            ->groupBy('course_offering_id')
            ->map(fn (Collection $rows): ?ResultSnapshot => $rows->sortByDesc('revision')->first())
            ->filter()
            ->map(fn (ResultSnapshot $snapshot): array => [
                'subject'            => $snapshot->courseOffering?->subject?->name,
                'academic_year_id'   => $snapshot->courseOffering?->academic_year_id,
                'academic_period_id' => $snapshot->courseOffering?->academic_period_id,
                'percentage'         => $snapshot->percentage,
                'revision'           => $snapshot->revision,
                'published_at'       => $snapshot->published_at,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function health(StudentRecord $enrollment): array
    {
        $record = StudentHealthRecord::query()->where('student_record_id', $enrollment->id)->first();

        if ($record === null) {
            return [];
        }

        return [
            'blood_group'             => $record->blood_group,
            'conditions'              => $record->conditions,
            'allergies'               => $record->allergies,
            'medications'             => $record->medications,
            'dietary_needs'           => $record->dietary_needs,
            'emergency_contact_name'  => $record->emergency_contact_name,
            'emergency_contact_phone' => $record->emergency_contact_phone,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function discipline(StudentRecord $enrollment): array
    {
        return $this->incidents($enrollment, restricted: false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function safeguarding(StudentRecord $enrollment): array
    {
        return $this->incidents($enrollment, restricted: true);
    }

    /**
     * Get the cases the student is named in, of one kind.
     *
     * @return array<int, array<string, mixed>>
     */
    private function incidents(StudentRecord $enrollment, bool $restricted): array
    {
        return Incident::query()
            ->where('is_restricted', $restricted)
            ->whereHas('participants', fn ($query) => $query->where('student_record_id', $enrollment->id))
            ->get()
            ->map(fn ($incident): array => [
                'reference'   => $incident->reference,
                'category'    => $incident->category->value,
                'status'      => $incident->status->value,
                'summary'     => $incident->summary,
                'occurred_at' => $incident->occurred_at,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function wellbeing(StudentRecord $enrollment): array
    {
        return SupportPlan::query()
            ->where('student_record_id', $enrollment->id)
            ->get()
            ->map(fn ($plan): array => [
                'title'     => $plan->title,
                'category'  => $plan->category->value,
                'status'    => $plan->status->value,
                'starts_on' => $plan->starts_on,
                'ends_on'   => $plan->ends_on,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function finance(StudentRecord $enrollment): array
    {
        return [
            'balance'          => $this->ledger->balance($enrollment),
            'unapplied_credit' => $this->ledger->unappliedCredit($enrollment),

            /*
             * The receiving school reads this as a notice of what is owed, and
             * to whom. It raises no invoice of its own: money owed to one
             * organization is not owed to another.
             */
            'owed_by_campus' => $this->ledger->balancesByCampus($enrollment)
                ->map(fn (array $row): array => [
                    'campus' => $row['school']->name,
                    'balance' => $row['balance'],
                ])
                ->all(),
        ];
    }
}
