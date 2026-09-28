<?php

namespace App\Services\Attendance;

use App\Actions\Attendance\RecordAttendance;
use App\Enums\AcademicStructureStatus;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Models\AcademicCycleSection;
use App\Models\AttendanceRecord;
use App\Models\CalendarEvent;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Calendar\SchoolCalendar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AttendanceRegister
{
    public function __construct(
        private RecordAttendance $recordAttendance,
        private SchoolCalendar $calendar,
    ) {}

    /**
     * Get the holiday or closure that shuts the section on the day.
     */
    public function closure(AcademicCycleSection $section, Carbon $date): ?CalendarEvent
    {
        return $this->calendar->closureOn($section->school_id, $section->id, $date);
    }

    /**
     * Get the sections a register can be opened for.
     *
     * These are the running sections of the working year. A draft or archived
     * section, or one of another year, has no class to call, so it is left
     * out unless it is the one already open.
     *
     * @return Collection<int, AcademicCycleSection>
     */
    public function sections(?int $openSectionId = null): Collection
    {
        return AcademicCycleSection::query()
            ->inSchool()
            ->where(fn ($offered) => $offered
                ->where(fn ($running) => $running
                    ->where('academic_year_id', current_academic_year_id())
                    ->where('status', AcademicStructureStatus::Active))
                ->when($openSectionId !== null, fn ($open) => $open->orWhereKey($openSectionId)))
            ->with('academicLevel:id,name')
            ->orderBy('name')
            ->get();
    }

    public function section(int $sectionId): ?AcademicCycleSection
    {
        return AcademicCycleSection::query()->inSchool()->with('academicLevel:id,name')->find($sectionId);
    }

    /**
     * Get the learners who take this register on the day.
     *
     * A learner who came from another campus after the day was on that
     * campus's register then, so they are left off this one.
     *
     * @return Collection<int, StudentRecord>
     */
    public function students(AcademicCycleSection $section, ?Carbon $date = null): Collection
    {
        $students = $section->currentEnrollments()
            ->attending()
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get();

        if ($date === null || $date->isToday()) {
            return $students;
        }

        return $students
            ->filter(fn (StudentRecord $student): bool => ($student->sectionOn($date)->school_id ?? $section->school_id) === $section->school_id)
            ->values();
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
