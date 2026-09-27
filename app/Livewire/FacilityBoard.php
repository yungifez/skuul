<?php

namespace App\Livewire;

use App\Actions\Facility\BookFacility;
use App\Actions\Facility\ManageFacility;
use App\Enums\FacilityKind;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The halls, laboratories, vehicles, and kit a campus shares, and who has
 * booked them next.
 */
class FacilityBoard extends Component
{
    use DispatchesStatusNotifications;

    public bool $isSharing = false;

    #[Locked]
    public ?int $editingId = null;

    public string $name = '';

    public string $kind = '';

    public string $capacity = '';

    public string $notes = '';

    public bool $isBooking = false;

    public string $facilityId = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public string $purpose = '';

    #[Locked]
    public ?int $givingUpId = null;

    public string $giveUpReason = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Facility::class);
    }

    public function startSharing(): void
    {
        Gate::authorize('create', Facility::class);

        $this->stopSharing();
        $this->isSharing = true;
        $this->kind = FacilityKind::Hall->value;
    }

    public function startChanging(int $facilityId): void
    {
        $facility = $this->facility($facilityId);
        Gate::authorize('update', $facility);

        $this->stopSharing();
        $this->editingId = $facility->id;
        $this->name = $facility->name;
        $this->kind = $facility->kind->value;
        $this->capacity = (string) $facility->capacity;
        $this->notes = (string) $facility->notes;
    }

    public function stopSharing(): void
    {
        $this->reset('isSharing', 'editingId', 'name', 'kind', 'capacity', 'notes');
        $this->resetValidation();
    }

    public function saveFacility(): void
    {
        $facility = $this->editingId === null ? null : $this->facility($this->editingId);

        if ($facility === null) {
            Gate::authorize('create', Facility::class);
        } else {
            Gate::authorize('update', $facility);
        }

        $this->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('facilities', 'name')->where('school_id', current_school_id())->ignore($facility?->id)],
            'kind' => ['required', Rule::in(FacilityKind::values())],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.unique' => 'This campus already shares something with that name.',
        ]);

        $values = [
            'name' => trim($this->name),
            'kind' => $this->kind,
            'capacity' => $this->capacity === '' ? null : (int) $this->capacity,
            'notes' => trim($this->notes) === '' ? null : trim($this->notes),
        ];

        if ($facility === null) {
            Facility::create($values + ['school_id' => current_school_id()]);
            $this->notify("{$values['name']} can be booked now.");
        } else {
            $facility->update($values);
            $this->notify('Saved.');
        }

        $this->stopSharing();
    }

    public function retire(int $facilityId, ManageFacility $manageFacility): void
    {
        $facility = $this->facility($facilityId);
        Gate::authorize('delete', $facility);

        $givenUp = $manageFacility->retire($facility, auth()->user());

        $this->notify($givenUp === 0
            ? "{$facility->name} is out of use."
            : "{$facility->name} is out of use. {$givenUp} ".str('booking')->plural($givenUp).' ahead '.($givenUp === 1 ? 'was' : 'were').' given up.');
    }

    public function restore(int $facilityId, ManageFacility $manageFacility): void
    {
        $facility = $this->facility($facilityId);
        Gate::authorize('update', $facility);

        $manageFacility->restore($facility);

        $this->notify("{$facility->name} can be booked again.");
    }

    public function startBooking(?int $facilityId = null): void
    {
        abort_unless(Gate::allows('book facility'), 403);

        $this->isBooking = true;
        $this->facilityId = $facilityId === null ? '' : (string) $facilityId;
    }

    public function stopBooking(): void
    {
        $this->reset('isBooking', 'facilityId', 'startsAt', 'endsAt', 'purpose');
        $this->resetValidation();
    }

    public function book(BookFacility $bookFacility): void
    {
        abort_unless(Gate::allows('book facility'), 403);

        $this->validate([
            'facilityId' => ['required', 'integer'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['required', 'date', 'after:startsAt'],
            'purpose' => ['required', 'string', 'max:255'],
        ], [
            'facilityId.required' => 'Choose what to book.',
            'endsAt.after' => 'A booking has to end after it starts.',
        ], [
            'startsAt' => 'from',
            'endsAt' => 'until',
            'purpose' => 'what it is for',
        ]);

        $facility = Facility::query()->inSchool()->find((int) $this->facilityId);

        if ($facility === null) {
            $this->addError('facilityId', 'Choose something this campus shares.');

            return;
        }

        Gate::authorize('book', $facility);

        try {
            $booking = $bookFacility->book($facility, Carbon::parse($this->startsAt), Carbon::parse($this->endsAt), trim($this->purpose), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('startsAt', $exception->getMessage());

            return;
        }

        $this->stopBooking();
        $this->notify("{$facility->name} is booked for {$booking->starts_at->format('j M, H:i')}.");
    }

    public function startGivingUp(int $bookingId): void
    {
        $booking = $this->booking($bookingId);
        $this->authorizeGivingUp($booking);

        $this->givingUpId = $booking->id;
        $this->giveUpReason = '';
        $this->resetValidation();
    }

    public function stopGivingUp(): void
    {
        $this->reset('givingUpId', 'giveUpReason');
        $this->resetValidation();
    }

    public function giveUp(BookFacility $bookFacility): void
    {
        if ($this->givingUpId === null) {
            return;
        }

        $booking = $this->booking($this->givingUpId);
        $this->authorizeGivingUp($booking);

        $this->validate(['giveUpReason' => ['nullable', 'string', 'max:255']]);

        try {
            $bookFacility->cancel($booking, $this->giveUpReason, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->stopGivingUp();
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->stopGivingUp();
        $this->notify('The booking was given up.');
    }

    public function render(): View
    {
        $facilities = Facility::query()->inSchool()->orderByDesc('is_active')->orderBy('name')->withCount([
            'bookings as upcoming_bookings_count' => fn ($query) => $query->running()->where('ends_at', '>=', now()),
        ])->get();

        return view('livewire.facility-board', [
            'facilities' => $facilities,
            'bookable' => $facilities->where('is_active', true),
            'bookings' => FacilityBooking::query()->inSchool()
                ->running()
                ->where('ends_at', '>=', now())
                ->with(['facility:id,name', 'bookedBy:id,name'])
                ->orderBy('starts_at')
                ->limit(50)
                ->get(),
            'kinds' => FacilityKind::cases(),
            'canManage' => Gate::allows('create', Facility::class),
            'canBook' => Gate::allows('book facility'),
            'userId' => auth()->id(),
        ]);
    }

    private function facility(int $facilityId): Facility
    {
        return Facility::query()->inSchool()->findOrFail($facilityId);
    }

    private function booking(int $bookingId): FacilityBooking
    {
        return FacilityBooking::query()->inSchool()->findOrFail($bookingId);
    }

    /**
     * Only the person who booked it, or whoever runs the facilities, may give
     * a booking up.
     */
    private function authorizeGivingUp(FacilityBooking $booking): void
    {
        $isOwnBooking = $booking->booked_by === auth()->id() && Gate::allows('book facility');

        abort_unless($isOwnBooking || Gate::allows('manage facility'), 403);
    }
}
