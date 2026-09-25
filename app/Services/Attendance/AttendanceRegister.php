<?php

namespace App\Services\Attendance;

use App\Actions\Attendance\RecordAttendance;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Models\AcademicCycleSection;
use App\Models\AttendanceRecord;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AttendanceRegister
{
    public function __construct(private RecordAttendance $recordAttendance) {}

    /** @return Collection<int, AcademicCycleSection> */
    public function sections(): Collection
    {
        return AcademicCycleSection::query()
            ->inSchool()
            ->with('academicLevel:id,name')
            ->orderBy('name')
            ->get();
    }

    public function section(int $sectionId): ?AcademicCycleSection
    {
        return AcademicCycleSection::query()->inSchool()->with('academicLevel:id,name')->find($sectionId);
    }

    /** @return Collection<int, StudentRecord> */
    public function students(AcademicCycleSection $section): Collection
    {
        return $section->currentEnrollments()
            ->attending()
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get();
    }

    /** @return array<int, AttendanceStatus> */
    public function statuses(AcademicCycleSection $section, Collection $students, Carbon $date): array
    {
        if ($students->isEmpty()) {
            return [];
        }

        return AttendanceRecord::query()
            ->onDate($date)
            ->ofKind(AttendanceKind::Daily)
            ->where('academic_cycle_section_id', $section->id)
            ->whereIn('student_record_id', $students->modelKeys())
            ->get()
            ->mapWithKeys(fn (AttendanceRecord $record): array => [
                $record->student_record_id => $record->status,
            ])
            ->all();
    }

    /**
     * @param  array<int, string>  $statusesByStudent
     * @param  Collection<int, StudentRecord>  $students
     */
    public function save(Collection $students, array $statusesByStudent, Carbon $date, ?User $actor): void
    {
        $studentIds = $students->modelKeys();
        $submittedIds = array_map('intval', array_keys($statusesByStudent));
        sort($studentIds);
        sort($submittedIds);

        if ($studentIds !== $submittedIds) {
            throw new \InvalidArgumentException('The class list changed. Refresh the register and try again.');
        }

        $entries = $students->map(fn (StudentRecord $student): array => [
            'enrollment' => $student,
            'status' => AttendanceStatus::from($statusesByStudent[$student->id]),
        ])->all();

        $this->recordAttendance->recordMany($entries, $date, actor: $actor);
    }
}
