<?php

namespace App\Livewire;

use App\Actions\Timetable\CreateSectionTimetableOverride;
use App\Actions\Timetable\CreateTimetableSubstitution;
use App\Enums\Role;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Work done beside a published timetable without changing it: a section's
 * own version, and cover for one lesson on one date.
 */
class TimetableCoverPanel extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public Timetable $timetable;

    public string $sectionId = '';

    public string $lesson = '';

    public string $teacherId = '';

    public string $coverDate = '';

    public string $reason = '';

    public function mount(Timetable $timetable): void
    {
        Gate::authorize('view', $timetable);

        $this->timetable = $timetable;
    }

    public function startOverride(CreateSectionTimetableOverride $createOverride): void
    {
        Gate::authorize('override', $this->timetable);

        $this->validate(['sectionId' => ['required', 'integer']], [], ['sectionId' => 'section']);

        $section = $this->overrideSections()->firstWhere('id', (int) $this->sectionId) ?? abort(404);

        try {
            $override = $createOverride->create($this->timetable->fresh() ?? $this->timetable, $section, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('sectionId', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Section timetable draft created from the published template.');
        $this->redirectRoute('timetables.manage', $override);
    }

    public function recordCover(CreateTimetableSubstitution $createSubstitution): void
    {
        Gate::authorize('substitute', $this->timetable);

        $this->reason = trim($this->reason);

        $this->validate([
            'lesson' => ['required', 'string', 'regex:/^\d+:\d+$/'],
            'teacherId' => ['required', 'integer'],
            'coverDate' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ], [], ['lesson' => 'scheduled lesson', 'teacherId' => 'covering teacher', 'coverDate' => 'date']);

        [$timeSlotId, $weekdayId] = array_map('intval', explode(':', $this->lesson, 2));
        $slot = TimetableTimeSlot::query()->where('timetable_id', $this->timetable->id)->find($timeSlotId);
        $teacher = $this->teachers()->firstWhere('id', (int) $this->teacherId);

        if ($slot === null) {
            $this->addError('lesson', 'Choose a scheduled entry from this timetable.');

            return;
        }

        if ($teacher === null) {
            $this->addError('teacherId', 'The replacement must be an active teacher at this school.');

            return;
        }

        try {
            $createSubstitution->create(
                $this->timetable->fresh() ?? $this->timetable,
                $slot,
                $weekdayId,
                User::query()->findOrFail($teacher->id),
                Carbon::parse($this->coverDate),
                $this->reason,
                auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('coverDate', $exception->getMessage());

            return;
        }

        $this->reset('lesson', 'teacherId', 'coverDate', 'reason');
        $this->notify('Cover recorded without changing the published timetable.');
    }

    public function withdrawCover(int $substitutionId, CreateTimetableSubstitution $createSubstitution): void
    {
        Gate::authorize('substitute', $this->timetable);

        $substitution = $this->timetable->substitutions()->findOrFail($substitutionId);

        try {
            $createSubstitution->withdraw($substitution, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('The cover was withdrawn.');
    }

    public function render(): View
    {
        $isPublished = $this->timetable->refresh()->status === TimetableStatus::Published;
        $canOverride = $isPublished && Gate::allows('override', $this->timetable);
        $canCover = $isPublished && Gate::allows('substitute', $this->timetable);

        return view('livewire.timetable-cover-panel', [
            'canOverride' => $canOverride,
            'canCover' => $canCover,
            'canWithdraw' => Gate::allows('substitute', $this->timetable),
            'overrideSections' => $canOverride ? $this->overrideSections() : collect(),
            'lessons' => $canCover ? $this->lessons() : collect(),
            'teachers' => $canCover ? $this->teachers() : collect(),
            'substitutions' => $this->timetable->substitutions()
                ->with(['timeSlot:id,start_time,stop_time', 'weekday:id,name', 'replacementTeacher:id,name', 'approvedBy:id,name'])
                ->latest('substituted_on')
                ->get(),
        ]);
    }

    /**
     * Get the other sections of the same level in the same cycle.
     *
     * @return Collection<int, AcademicCycleSection>
     */
    private function overrideSections()
    {
        $section = $this->timetable->academicCycleSection;

        if ($section === null) {
            return collect();
        }

        return AcademicCycleSection::query()
            ->inSchool()
            ->where('academic_year_id', $section->academic_year_id)
            ->where('academic_level_id', $section->academic_level_id)
            ->whereKeyNot($section->id)
            ->with('academicLevel:id,name')
            ->orderBy('position')
            ->get(['id', 'name', 'label', 'academic_level_id', 'school_id', 'academic_year_id']);
    }

    /**
     * Get every scheduled lesson of this timetable.
     *
     * @return Collection<int, TimetableRecord>
     */
    private function lessons()
    {
        return TimetableRecord::query()
            ->join('timetable_time_slots', 'timetable_time_slot_weekday.timetable_time_slot_id', '=', 'timetable_time_slots.id')
            ->join('weekdays', 'timetable_time_slot_weekday.weekday_id', '=', 'weekdays.id')
            ->where('timetable_time_slots.timetable_id', $this->timetable->id)
            ->orderBy('timetable_time_slot_weekday.weekday_id')
            ->orderBy('timetable_time_slots.start_time')
            ->get([
                'timetable_time_slot_weekday.timetable_time_slot_id',
                'timetable_time_slot_weekday.weekday_id',
                'timetable_time_slots.start_time',
                'timetable_time_slots.stop_time',
                'weekdays.name as weekday_name',
            ]);
    }

    /**
     * Get the teachers who work at this campus.
     *
     * @return Collection<int, User>
     */
    private function teachers()
    {
        return User::ofSchool()->role(Role::Teacher->value)->orderBy('name')->get(['users.id', 'users.name']);
    }
}
