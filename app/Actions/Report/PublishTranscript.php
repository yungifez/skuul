<?php

namespace App\Actions\Report;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\TranscriptSnapshot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PublishTranscript
{
    public function __construct(private RecordAuditEvent $audit)
    {
    }

    /** @throws InvalidValueException */
    public function publish(StudentRecord $studentRecord, User $actor, ?string $reason = null): TranscriptSnapshot
    {
        return DB::transaction(function () use ($studentRecord, $actor, $reason): TranscriptSnapshot {
            $results = ResultSnapshot::query()->where('student_record_id', $studentRecord->id)->with('courseOffering.subject:id,name,short_name', 'courseOffering.academicYear:id,start_year,stop_year', 'courseOffering.academicPeriod:id,name,label')->orderBy('course_offering_id')->orderByDesc('revision')->get()->unique('course_offering_id')->values();
            if ($results->isEmpty()) {
                throw new InvalidValueException('Publish at least one subject result before issuing a transcript.');
            }
            $previous = TranscriptSnapshot::query()->where('student_record_id', $studentRecord->id)->lockForUpdate()->latest('revision')->first();
            $transcript = TranscriptSnapshot::create(['school_id' => $studentRecord->school_id, 'student_record_id' => $studentRecord->id, 'revision' => $previous === null ? 1 : $previous->revision + 1, 'payload' => ['student' => ['id' => $studentRecord->id, 'admission_number' => $studentRecord->admission_number], 'results' => $results->map(fn (ResultSnapshot $result): array => ['source_result_snapshot_id' => $result->id, 'source_revision' => $result->revision, 'academic_year' => $result->courseOffering->academicYear->name, 'academic_period' => $result->courseOffering->academicPeriod->label ?? $result->courseOffering->academicPeriod->name, 'subject' => $result->courseOffering->subject->name, 'percentage' => $result->percentage])->all()], 'reason' => $reason, 'issued_at' => now(), 'issued_by' => $actor->id]);
            $this->audit->record($previous === null ? AuditAction::TranscriptIssued : AuditAction::TranscriptRevised, $transcript, ['student_record_id' => $studentRecord->id, 'revision' => $transcript->revision], $actor);

            return $transcript;
        });
    }
}
