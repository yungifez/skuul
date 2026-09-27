<?php

namespace App\Livewire;

use App\Actions\Calendar\GenerateAcademicCycle;
use App\Actions\Calendar\SetCampusCalendarTemplate;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Draft a campus school year from a template, and choose which campuses follow it.
 */
class CalendarTemplateCampuses extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public Organization $organization;

    #[Locked]
    public CalendarTemplate $calendarTemplate;

    public string $schoolId = '';

    public string $startsOn = '';

    /**
     * The campus whose calendar choice is open, and why it changes.
     */
    public ?int $changingSchoolId = null;

    public string $reason = '';

    public function mount(Organization $organization, CalendarTemplate $calendarTemplate): void
    {
        Gate::authorize('manageCalendar', $organization);
        abort_unless($calendarTemplate->organization_id === $organization->id, 404);

        $this->organization = $organization;
        $this->calendarTemplate = $calendarTemplate;
        $this->schoolId = (string) $this->campuses()->first()?->id;
    }

    public function generate(GenerateAcademicCycle $generateAcademicCycle): void
    {
        Gate::authorize('manageCalendar', $this->organization);

        $this->validate([
            'schoolId' => ['required', Rule::in($this->campusIds())],
            'startsOn' => ['required', 'date_format:Y-m-d'],
        ], [
            'schoolId.in' => 'Choose one of this organization’s campuses.',
        ], [
            'schoolId' => 'campus',
            'startsOn' => 'start date',
        ]);

        $school = $this->campuses()->firstWhere('id', (int) $this->schoolId);

        try {
            $year = $generateAcademicCycle->generate($school, Carbon::parse($this->startsOn), $this->calendarTemplate, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('startsOn', $exception->getMessage());

            return;
        }

        $this->startsOn = '';
        $this->notify("Drafted {$year->name} for {$school->name} from {$this->calendarTemplate->name}.");
    }

    public function startChanging(int $schoolId): void
    {
        Gate::authorize('manageCalendar', $this->organization);

        $this->changingSchoolId = in_array((string) $schoolId, $this->campusIds(), true) ? $schoolId : null;
        $this->reason = '';
        $this->resetValidation('reason');
    }

    public function cancelChanging(): void
    {
        $this->changingSchoolId = null;
        $this->reason = '';
        $this->resetValidation('reason');
    }

    /**
     * Point the open campus at this template, or return it to the organization default when it follows this one already.
     */
    public function saveChange(SetCampusCalendarTemplate $setCampusCalendarTemplate): void
    {
        Gate::authorize('manageCalendar', $this->organization);

        $this->reason = trim($this->reason);
        $this->validate(['reason' => ['required', 'string', 'max:500']], [
            'reason.required' => 'Say why, so the audit trail explains the change.',
        ]);

        $school = $this->campuses()->firstWhere('id', $this->changingSchoolId);

        if ($school === null) {
            $this->cancelChanging();

            return;
        }

        try {
            if ($school->calendar_template_id === $this->calendarTemplate->id) {
                $setCampusCalendarTemplate->inherit($school, auth()->user(), $this->reason);
                $message = "{$school->name} now follows the organization default calendar.";
            } else {
                $setCampusCalendarTemplate->override($school, $this->calendarTemplate, auth()->user(), $this->reason);
                $message = "{$school->name} now follows {$this->calendarTemplate->name}.";
            }
        } catch (InvalidValueException $exception) {
            $this->addError('reason', $exception->getMessage());

            return;
        }

        $this->cancelChanging();
        $this->notify($message);
    }

    public function render(): View
    {
        return view('livewire.calendar-template-campuses', [
            'campuses' => $this->campuses(),
        ]);
    }

    /**
     * @return Collection<int, School>
     */
    private function campuses(): Collection
    {
        return School::query()
            ->where('organization_id', $this->organization->id)
            ->with('calendarTemplate:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'organization_id', 'calendar_template_id']);
    }

    /**
     * @return array<int, string>
     */
    private function campusIds(): array
    {
        return $this->campuses()->pluck('id')->map(fn (int $id): string => (string) $id)->all();
    }
}
