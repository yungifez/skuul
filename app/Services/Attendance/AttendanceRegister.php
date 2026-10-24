<?php

namespace App\Services\Attendance;

use App\Actions\Attendance\RecordAttendance;
use App\Enums\AcademicStructureStatus;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\CourseOfferingStatus;
use App\Models\AcademicCycleSection;
use App\Models\AttendanceRecord;
use App\Models\CalendarEvent;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Services\Calendar\SchoolCalendar;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AttendanceRegister
{
    public function __construct(
        private RecordAttendance $recordAttendance,
        private SchoolCalendar $calendar,
        private CourseOfferingRoster $roster,
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
            ->orderedByLevel()
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
        $this->failIfTheListChanged($students, $statusesByStudent);

        $entries = $students->map(fn (StudentRecord $student): array => [
            'enrollment' => $student,
            'status' => AttendanceStatus::from($statusesByStudent[$student->id]),
        ])->all();

        $this->recordAttendance->recordMany($entries, $date, actor: $actor);
    }

    /**
     * Get the offerings whose lesson register the person may take on the day.
     *
     * These are the running offerings of the working school whose term
     * covers the day, or the current term when no term has dates.
     *
     * @return Collection<int, CourseOffering>
     */
    public function lessonOfferings(User $user, Carbon $date): Collection
    {
        return CourseOffering::query()
            ->inSchool()
            ->with(['subject:id,name,school_id', 'academicLevel:id,name'])
            ->where('status', '!=', CourseOfferingStatus::Archived)
            ->whereHas('academicPeriod', fn ($periods) => $periods
                ->where(fn ($dated) => $dated
                    ->whereDate('starts_on', '<=', $date)
                    ->whereDate('ends_on', '>=', $date))
                ->orWhereKey(current_academic_period_id()))
            ->orderBy('academic_level_id')
            ->orderBy('subject_id')
            ->get()
            ->filter(fn (CourseOffering $offering): bool => $user->can('takeLessonRegister', $offering))
            ->values();
    }

    public function lessonOffering(int $courseOfferingId): ?CourseOffering
    {
        return CourseOffering::query()->inSchool()->with(['subject:id,name,school_id', 'academicLevel:id,name'])->find($courseOfferingId);
    }

    /**
     * Get the learners on the offering's roster who attend on the day.
     *
     * @return Collection<int, StudentRecord>
     */
    public function lessonStudents(CourseOffering $courseOffering): Collection
    {
        return StudentRecord::query()
            ->inSchool($courseOffering->school_id)
            ->whereKey($this->roster->students($courseOffering)->pluck('id')->all())
            ->attending()
            ->with(['user:id,name', 'academicCycleSection.academicLevel:id,name'])
            ->orderBy('admission_number')
            ->get();
    }

    /**
     * Get the lesson times of the offering on the day, from its published timetables.
     *
     * @return Collection<int, TimetableTimeSlot>
     */
    public function lessonTimes(CourseOffering $courseOffering, Carbon $date): Collection
    {
        $timetables = Timetable::query()
            ->published()
            ->where('academic_period_id', $courseOffering->academic_period_id)
            ->where(fn ($owners) => $owners
                ->where('course_offering_id', $courseOffering->id)
                ->orWhereIn('academic_cycle_section_id', $courseOffering->reachedSectionIds()))
            ->pluck('id');

        return TimetableTimeSlot::query()
            ->whereIn('timetable_id', $timetables)
            ->whereHas('weekdays', fn ($cells) => $cells
                ->where('weekdays.id', $date->dayOfWeekIso)
                ->where('timetable_time_slot_weekdayable_type', $courseOffering->subject->getMorphClass())
                ->where('timetable_time_slot_weekdayable_id', $courseOffering->subject_id))
            ->orderBy('start_time')
            ->get()
            ->filter(fn (TimetableTimeSlot $slot): bool => $slot->occursOn($date, $date->dayOfWeekIso))
            ->values();
    }

    /**
     * @param  Collection<int, StudentRecord>  $students
     * @return array<int, AttendanceStatus>
     */
    public function lessonStatuses(CourseOffering $courseOffering, ?TimetableTimeSlot $timeSlot, Collection $students, Carbon $date): array
    {
        if ($students->isEmpty()) {
            return [];
        }

        return AttendanceRecord::query()
            ->onDate($date)
            ->where('register_key', AttendanceRecord::registerKey(AttendanceKind::Period, $courseOffering->id, $timeSlot?->id))
            ->whereIn('student_record_id', $students->modelKeys())
            ->get()
            ->mapWithKeys(fn (AttendanceRecord $record): array => [
                $record->student_record_id => $record->status,
            ])
            ->all();
    }

    /**
     * Save one lesson register whole.
     *
     * @param  array<int, string>  $statusesByStudent
     * @param  Collection<int, StudentRecord>  $students
     */
    public function saveLesson(CourseOffering $courseOffering, ?TimetableTimeSlot $timeSlot, Collection $students, array $statusesByStudent, Carbon $date, ?User $actor): void
    {
        $this->failIfTheListChanged($students, $statusesByStudent);

        $entries = $students->map(fn (StudentRecord $student): array => [
            'enrollment' => $student,
            'status' => AttendanceStatus::from($statusesByStudent[$student->id]),
        ])->all();

        $this->recordAttendance->recordMany($entries, $date, AttendanceKind::Period, actor: $actor, courseOffering: $courseOffering, timeSlot: $timeSlot);
    }

    /**
     * Refuse a register whose list of learners changed while it was open.
     *
     * @param  Collection<int, StudentRecord>  $students
     * @param  array<int, string>  $statusesByStudent
     */
    private function failIfTheListChanged(Collection $students, array $statusesByStudent): void
    {
        $studentIds = $students->modelKeys();
        $submittedIds = array_map('intval', array_keys($statusesByStudent));
        sort($studentIds);
        sort($submittedIds);

        if ($studentIds !== $submittedIds) {
            throw new \InvalidArgumentException('The class list changed. Refresh the register and try again.');
        }
    }
}
