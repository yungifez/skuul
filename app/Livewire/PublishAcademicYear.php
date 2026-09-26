<?php

namespace App\Livewire;

use App\Actions\Academic\PublishAcademicCalendar;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PublishAcademicYear extends Component
{
    #[Locked]
    public AcademicYear $academicYear;

    public function mount(AcademicYear $academicYear): void
    {
        Gate::authorize('update', $academicYear);

        $this->academicYear = $academicYear;
    }

    public function render(): View
    {
        return view('livewire.publish-academic-year');
    }

    public function publish(PublishAcademicCalendar $publishAcademicCalendar, AcademicYearService $academicYears): void
    {
        Gate::authorize('update', $this->academicYear);

        try {
            $academicYear = $publishAcademicCalendar->publish($this->academicYear, auth()->user());
            $academicYears->setSchoolDefaultAcademicYear($academicYear);
        } catch (InvalidValueException $exception) {
            $this->addError('setup', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The academic year is ready. Its school calendar is now available.');

        $this->redirectRoute('academic-years.show', $academicYear);
    }
}
