<?php

namespace App\Services\Attendance;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\StudentRecord;
use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * Count what the registers say about a student.
 *
 * A day nobody recorded is left out of the rate. Counting it as an absence
 * would blame the family for the school's missing register.
 */
class AttendanceSummary
{
    /**
     * Summarise one student between two days.
     *
     * @return array{present: int, absent: int, late: int, excused: int, recorded: int, rate: float|null}
     */
    public function forStudent(
        StudentRecord $enrollment,
        DateTimeInterface|string|null $from = null,
        DateTimeInterface|string|null $to = null,
        AttendanceKind $kind = AttendanceKind::Daily,
    ): array {
        $records = AttendanceRecord::query()
            ->where('student_record_id', $enrollment->id)
            ->ofKind($kind)
            ->when($from !== null, fn ($query) => $query->whereDate('attended_on', '>=', Carbon::parse($from)->toDateString()))
            ->when($to !== null, fn ($query) => $query->whereDate('attended_on', '<=', Carbon::parse($to)->toDateString()))
            ->get();

        $counted = $records->filter(fn (AttendanceRecord $record): bool => $record->status->countsInRate());
        $present = $counted->filter(fn (AttendanceRecord $record): bool => $record->status->countsAsPresent());

        return [
            'present' => $present->count(),
            'absent' => $counted->where('status', AttendanceStatus::Absent)->count(),
            'late' => $counted->where('status', AttendanceStatus::Late)->count(),
            'excused' => $counted->where('status', AttendanceStatus::Excused)->count(),
            'recorded' => $counted->count(),
            'rate' => $counted->isEmpty() ? null : round(($present->count() / $counted->count()) * 100, 2),
        ];
    }
}
