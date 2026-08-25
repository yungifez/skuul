<?php

namespace App\Http\Controllers;

use App\Actions\Library\CloseReservation;
use App\Actions\Library\ReserveTitle;
use App\Enums\LibraryReservationStatus;
use App\Http\Requests\StoreLibraryReservationRequest;
use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The queue for titles everybody wants.
 */
class LibraryReservationController extends Controller
{
    /**
     * Show who is waiting and what is behind the desk.
     */
    public function index(): View
    {
        $this->authorize('viewAny', LibraryReservation::class);

        return view('pages.library.reservations', [
            'ready' => LibraryReservation::inSchool()
                ->where('status', LibraryReservationStatus::Ready->value)
                ->with(['title', 'borrower', 'copy'])
                ->orderBy('holds_until')
                ->get(),
            'waiting' => LibraryReservation::inSchool()
                ->where('status', LibraryReservationStatus::Waiting->value)
                ->with(['title', 'borrower'])
                ->orderBy('id')
                ->get(),
            'titles' => LibraryTitle::forSchool()
                ->whereHas('copies', fn ($query) => $query->where('school_id', current_school_id()))
                ->orderBy('title')
                ->get(),
            'borrowers' => $this->borrowers(),
            'canManage' => auth()->user()?->can('lend library item') === true,
        ]);
    }

    /**
     * Put somebody in the queue.
     */
    public function store(StoreLibraryReservationRequest $request, ReserveTitle $reserve): RedirectResponse
    {
        $this->authorize('create', LibraryReservation::class);

        $reservation = $reserve->reserve(
            LibraryTitle::forSchool()
                ->whereHas('copies', fn ($query) => $query->where('school_id', current_school_id()))
                ->findOrFail($request->integer('library_title_id')),
            User::findOrFail($request->integer('user_id')),
            $request->user(),
            schoolId: current_school_id(),
        );

        return back()->with('success', $reservation->status->isOpen() && $reservation->library_copy_id !== null
            ? 'A copy is behind the desk for them.'
            : 'They are in the queue.');
    }

    /**
     * Take a reservation off.
     */
    public function destroy(LibraryReservation $libraryReservation, CloseReservation $close): RedirectResponse
    {
        $this->authorize('delete', $libraryReservation);

        $close->cancel($libraryReservation, request()->user());

        return back()->with('success', 'The reservation is off.');
    }

    /**
     * Get everybody this campus may lend to.
     *
     * @return Collection<int, User>
     */
    private function borrowers(): Collection
    {
        return User::query()
            ->ofSchool()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toBase();
    }
}
