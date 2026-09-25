<?php

namespace App\Services\Report;

use App\Models\StudentRecord;
use App\Models\TranscriptSnapshot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * List issued transcripts and learners in the working school.
 */
class TranscriptDirectory
{
    /** @return Collection<int, StudentRecord> */
    public function students(): Collection
    {
        return StudentRecord::query()
            ->inSchool()
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get(['id', 'user_id', 'admission_number']);
    }

    public function transcripts(?int $studentRecordId): LengthAwarePaginator
    {
        return TranscriptSnapshot::query()
            ->inSchool()
            ->with('studentRecord.user:id,name')
            ->when($studentRecordId !== null, function (Builder $query) use ($studentRecordId): void {
                $query->where('student_record_id', $studentRecordId);
            })
            ->latest('issued_at')
            ->paginate(20);
    }
}
