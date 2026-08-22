<?php

namespace App\Actions\Report;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\ReportCardSnapshot;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublishReportCard
{
    public function __construct(private RecordAuditEvent $audit) {}

    /** @throws InvalidValueException */
    public function publish(StudentRecord $studentRecord, AcademicPeriod $academicPeriod, User $actor, ?string $reason = null): ReportCardSnapshot
    {
        if ($studentRecord->school_id !== $academicPeriod->school_id || $academicPeriod->academic_year_id === null) {
            throw new InvalidValueException('Choose a student and academic period from the same school.');
        }

        if (!in_array($academicPeriod->status, [AcademicPeriodStatus::Closing, AcademicPeriodStatus::Closed, AcademicPeriodStatus::Archived], true)) {
            throw new InvalidValueException('Report cards can be published only while an academic period is closing or finished.');
        }

        return DB::transaction(function () use ($studentRecord, $academicPeriod, $actor, $reason): ReportCardSnapshot {
            $results = $this->latestResults($studentRecord, $academicPeriod);

            if ($results->isEmpty()) {
                throw new InvalidValueException('Publish at least one subject result before publishing this report card.');
            }

            $previous = ReportCardSnapshot::query()
                ->where('student_record_id', $studentRecord->id)
                ->where('academic_period_id', $academicPeriod->id)
                ->lockForUpdate()
                ->orderByDesc('revision')
                ->first();
            $payload = $this->payload($studentRecord, $academicPeriod, $results);
            $reportCard = ReportCardSnapshot::create([
                'school_id' => $studentRecord->school_id,
                'student_record_id' => $studentRecord->id,
                'academic_year_id' => $academicPeriod->academic_year_id,
                'academic_period_id' => $academicPeriod->id,
                'revision' => $previous === null ? 1 : $previous->revision + 1,
                'average_percentage' => $payload['summary']['average_percentage'],
                'payload' => $payload,
                'reason' => $reason,
                'published_at' => now(),
                'published_by' => $actor->id,
            ]);
            $this->audit->record($previous === null ? AuditAction::ReportCardPublished : AuditAction::ReportCardRevised, $reportCard, ['student_record_id' => $studentRecord->id, 'academic_period_id' => $academicPeriod->id, 'revision' => $reportCard->revision], $actor);

            return $reportCard;
        });
    }

    /** @return Collection<int, ResultSnapshot> */
    private function latestResults(StudentRecord $studentRecord, AcademicPeriod $academicPeriod): Collection
    {
        return ResultSnapshot::query()->where('student_record_id', $studentRecord->id)
            ->whereHas('courseOffering', fn ($query) => $query->where('academic_period_id', $academicPeriod->id))
            ->with('courseOffering.subject:id,name,short_name')
            ->orderBy('course_offering_id')->orderByDesc('revision')->get()->unique('course_offering_id')->values();
    }

    /** @param Collection<int, ResultSnapshot> $results @return array<string, mixed> */
    private function payload(StudentRecord $studentRecord, AcademicPeriod $academicPeriod, Collection $results): array
    {
        $percentages = $results->pluck('percentage')->filter(fn (?float $percentage): bool => $percentage !== null);

        return ['student' => ['id' => $studentRecord->id, 'admission_number' => $studentRecord->admission_number], 'academic_period' => ['id' => $academicPeriod->id, 'name' => $academicPeriod->label ?? $academicPeriod->name], 'results' => $results->map(fn (ResultSnapshot $result): array => ['course_offering_id' => $result->course_offering_id, 'source_result_snapshot_id' => $result->id, 'source_revision' => $result->revision, 'subject' => ['name' => $result->courseOffering->subject->name, 'short_name' => $result->courseOffering->subject->short_name], 'percentage' => $result->percentage, 'result' => $result->payload])->all(), 'summary' => ['subjects_reported' => $results->count(), 'average_percentage' => $percentages->isEmpty() ? null : round((float) $percentages->avg(), 2)]];
    }
}
