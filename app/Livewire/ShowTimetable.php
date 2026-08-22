<?php

namespace App\Livewire;

use App\Enums\RosterMode;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
use App\Services\Timetable\TimetableService;
use Livewire\Component;

class ShowTimetable extends Component
{
    public Timetable $timetable;

    public $weekDays;

    public $timeSlots;

    public $weekdays;

    public $subjects;

    public $customItems;

    public bool $disableEmitCellInformationDetail = true;

    /**
     * Determine if to show the timetable.
     */
    public bool $showDescription = true;

    public function mount(TimetableService $timetableService): void
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time')->load('weekdays');
        $this->weekdays = Weekday::all();
        $cycleSection = $this->timetable->load('academicCycleSection')->academicCycleSection;
        $this->subjects = CourseOffering::inSchool()
            ->where('academic_period_id', $this->timetable->academic_period_id)
            ->where(function ($query) use ($cycleSection): void {
                $query->whereHas('cycleSections', fn ($sections) => $sections->whereKey($cycleSection->id))
                    ->orWhere(function ($offerings) use ($cycleSection): void {
                        $offerings->where('roster_mode', RosterMode::AcademicLevel)
                            ->where('academic_level_id', $cycleSection->academic_level_id);
                    });
            })
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->filter(fn (?Subject $subject): bool => $subject !== null)
            ->unique('id')
            ->values();
        $this->customItems = $timetableService->getAllCustomTimetableItem();
    }

    public function emitCellInformationDetail(TimetableTimeSlot $timeSlot, Weekday $weekday)
    {
        $this->dispatch('timetableCellClicked', ['timeSlot' => $timeSlot, 'weekday' => $weekday]);
    }

    public function render()
    {
        return view('livewire.show-timetable');
    }
}
