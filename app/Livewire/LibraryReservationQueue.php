<?php

namespace App\Livewire;

use App\Actions\Library\CloseReservation;
use App\Actions\Library\ReserveTitle;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Who is waiting for which title, and what is behind the desk for them.
 */
class LibraryReservationQueue extends Component
{
    use DispatchesStatusNotifications;

    public bool $isAdding = false;

    public string $titleId = '';

    public string $borrowerSearch = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', LibraryReservation::class);
    }

    public function startAdding(): void
    {
        Gate::authorize('create', LibraryReservation::class);

        $this->isAdding = true;
    }

    public function stopAdding(): void
    {
        $this->reset('isAdding', 'titleId', 'borrowerSearch');
        $this->resetValidation();
    }

    public function reserveFor(int $userId, ReserveTitle $reserveTitle): void
    {
        Gate::authorize('create', LibraryReservation::class);

        $this->validate(['titleId' => ['required', 'integer']], ['titleId.required' => 'Choose the title first.']);

        $title = $this->titlesOfThisCampus()->firstWhere('id', (int) $this->titleId);
        $borrower = User::query()->ofSchool()->find($userId);

        if ($title === null) {
            $this->addError('titleId', 'Choose a title from the list.');

            return;
        }

        if ($borrower === null) {
            $this->addError('borrowerSearch', 'This person does not belong to this campus.');

            return;
        }

        try {
            $reservation = $reserveTitle->reserve($title, $borrower, auth()->user(), current_school_id());
        } catch (InvalidValueException $exception) {
            $this->addError('borrowerSearch', $exception->getMessage());

            return;
        }

        $this->stopAdding();
        $this->notify($reservation->status === LibraryReservationStatus::Ready
            ? "A copy of {$title->title} is behind the desk for {$borrower->name}."
            : "{$borrower->name} is number {$reservation->placeInQueue()} for {$title->title}.");
    }

    public function takeOff(int $reservationId, CloseReservation $closeReservation): void
    {
        $reservation = LibraryReservation::query()->inSchool()->findOrFail($reservationId);
        Gate::authorize('delete', $reservation);

        try {
            $closeReservation->cancel($reservation, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('The reservation is off.');
    }

    public function render(): View
    {
        return view('livewire.library-reservation-queue', [
            'ready' => LibraryReservation::query()->inSchool()
                ->where('status', LibraryReservationStatus::Ready->value)
                ->with(['title:id,title', 'borrower:id,name', 'copy:id,barcode'])
                ->orderBy('holds_until')
                ->get(),
            'waiting' => LibraryReservation::query()->inSchool()
                ->where('status', LibraryReservationStatus::Waiting->value)
                ->with(['title:id,title', 'borrower:id,name'])
                ->orderBy('library_title_id')
                ->orderBy('id')
                ->get(),
            'titles' => $this->isAdding ? $this->titlesOfThisCampus() : collect(),
            'borrowers' => $this->isAdding ? $this->matchingBorrowers() : collect(),
            'canManage' => Gate::allows('create', LibraryReservation::class),
        ]);
    }

    /**
     * @return Collection<int, LibraryTitle>
     */
    private function titlesOfThisCampus(): Collection
    {
        return LibraryTitle::forSchool()
            ->whereHas('copies', fn (Builder $query) => $query->where('school_id', current_school_id()))
            ->orderBy('title')
            ->get(['id', 'title'])
            ->toBase();
    }

    /**
     * People of this campus whose name or admission number matches.
     *
     * @return Collection<int, User>
     */
    private function matchingBorrowers(): Collection
    {
        $search = trim($this->borrowerSearch);

        if (mb_strlen($search) < 2) {
            return collect();
        }

        return User::query()
            ->ofSchool()
            ->where(fn (Builder $match) => $match
                ->where('name', 'like', "%{$search}%")
                ->orWhereHas('studentRecords', fn (Builder $enrollment) => $enrollment
                    ->where('school_id', current_school_id())
                    ->where('admission_number', 'like', "%{$search}%")))
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name'])
            ->toBase();
    }
}
