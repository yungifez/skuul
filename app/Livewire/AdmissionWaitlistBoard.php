<?php

namespace App\Livewire;

use App\Actions\Admissions\AcceptWaitlistEntry;
use App\Actions\Admissions\DeclineWaitlistEntry;
use App\Actions\Admissions\JoinWaitlist;
use App\Actions\Admissions\OfferNextWaitlistEntry;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreAdmissionWaitlistRequest;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\User;
use App\Services\Admissions\AdmissionWaitlistDirectory;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class AdmissionWaitlistBoard extends Component
{
    public ?int $academic_cycle_section_id = null;

    public ?int $user_id = null;

    public int $priority = 0;

    public function mount(): void
    {
        Gate::authorize('viewAny', AdmissionWaitlistEntry::class);
    }

    public function render(AdmissionWaitlistDirectory $directory): View
    {
        return view('livewire.admission-waitlist-board', $directory->forWorkingSchool());
    }

    public function addCandidate(JoinWaitlist $join): void
    {
        Gate::authorize('create', AdmissionWaitlistEntry::class);

        $validated = $this->validate(StoreAdmissionWaitlistRequest::waitlistRulesForWorkingSchool());
        $section = AcademicCycleSection::inSchool()->findOrFail($validated['academic_cycle_section_id']);
        $candidate = User::ofSchool()->findOrFail($validated['user_id']);

        try {
            $join->join($section, $candidate, auth()->user(), (int) ($validated['priority'] ?? 0));
        } catch (InvalidValueException $exception) {
            $this->addError('waitlist', $exception->getMessage());

            return;
        }

        $this->reset(['user_id', 'priority']);
        session()->flash('success', 'The candidate is on the admission waitlist.');
    }

    public function offer(int $entryId, OfferNextWaitlistEntry $offer): void
    {
        $entry = AdmissionWaitlistEntry::inSchool()->findOrFail($entryId);
        Gate::authorize('update', $entry);

        $offered = $offer->offer($entry->academicCycleSection, auth()->user());

        session()->flash($offered === null ? 'info' : 'success', $offered === null
            ? 'There is no open place or pending candidate for this section.'
            : 'The next candidate has been offered a place.');
    }

    public function accept(int $entryId, AcceptWaitlistEntry $accept): void
    {
        $entry = AdmissionWaitlistEntry::inSchool()->findOrFail($entryId);
        Gate::authorize('update', $entry);

        try {
            $accept->accept($entry, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('waitlist', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The candidate accepted the place and is now enrolled.');
    }

    public function decline(int $entryId, DeclineWaitlistEntry $decline): void
    {
        $entry = AdmissionWaitlistEntry::inSchool()->findOrFail($entryId);
        Gate::authorize('update', $entry);

        try {
            $decline->decline($entry, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('waitlist', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The admission waitlist entry was declined.');
    }
}
