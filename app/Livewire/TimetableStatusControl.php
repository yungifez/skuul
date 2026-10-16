<?php

namespace App\Livewire;

use App\Actions\Timetable\PublishTimetable;
use App\Actions\Timetable\ReviseTimetable;
use App\Enums\TimetableStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Timetable;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show where a timetable stands, and publish it or start its next revision.
 */
class TimetableStatusControl extends Component
{
    use DispatchesStatusNotifications;

    public Timetable $timetable;

    public function publish(PublishTimetable $publishTimetable): void
    {
        Gate::authorize('publish', $this->timetable);

        try {
            $publishTimetable->publish($this->timetable, auth()->user());
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', 'Timetable published.');
        $this->redirect(url()->previous(), navigate: false);
    }

    public function revise(ReviseTimetable $reviseTimetable): void
    {
        Gate::authorize('revise', $this->timetable);

        try {
            $draft = $reviseTimetable->revise($this->timetable, auth()->user());
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', 'New timetable revision created.');
        $this->redirectRoute('timetables.manage', $draft);
    }

    public function render(): View
    {
        $this->timetable->loadMissing('academicPeriod.academicYear');
        $period = $this->timetable->academicPeriod;
        $acceptsChanges = (bool) ($period?->isOpen() && $period->academicYear?->isOpen());
        $status = $this->timetable->status;

        return view('livewire.timetable-status-control', [
            'status' => $status,
            'acceptsChanges' => $acceptsChanges,
            'canPublish' => $status === TimetableStatus::Draft && $acceptsChanges && Gate::allows('publish', $this->timetable),
            'canRevise' => $status === TimetableStatus::Published && $acceptsChanges && Gate::allows('revise', $this->timetable),
        ]);
    }
}
