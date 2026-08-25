<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryReservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * End a reservation, and pass the copy to the next person waiting.
 *
 * A reservation ends when it is collected, when the person gives it up, or
 * when nobody came for it in time. In every case the copy it was holding goes
 * straight to whoever is next, so a book is never left behind the desk for
 * somebody who is not coming.
 */
class CloseReservation
{
    public function __construct(
        private HoldCopyForNextInQueue $hold,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Mark the reservation collected, once the copy has been issued.
     */
    public function collected(LibraryReservation $reservation, ?User $actor = null): LibraryReservation
    {
        return $this->close($reservation, LibraryReservationStatus::Collected, $actor);
    }

    /**
     * Take the reservation off at the borrower's or the library's request.
     *
     * @throws InvalidValueException when the reservation has already ended
     */
    public function cancel(LibraryReservation $reservation, ?User $actor = null): LibraryReservation
    {
        if (!$reservation->isOpen()) {
            throw new InvalidValueException('This reservation has already ended.');
        }

        return $this->close($reservation, LibraryReservationStatus::Cancelled, $actor);
    }

    /**
     * Give up on a hold nobody came for.
     */
    public function expire(LibraryReservation $reservation, ?User $actor = null): LibraryReservation
    {
        return $this->close($reservation, LibraryReservationStatus::Expired, $actor);
    }

    /**
     * Write the ending and offer the copy to the next person.
     */
    private function close(LibraryReservation $reservation, LibraryReservationStatus $status, ?User $actor): LibraryReservation
    {
        return DB::transaction(function () use ($reservation, $status, $actor): LibraryReservation {
            $reservation->loadMissing('title');
            $title = $reservation->title;

            $reservation->status = $status;
            $reservation->closed_on = now();

            // The copy is only let go when nobody took it. A collected
            // reservation keeps the copy it names, which is what was borrowed.
            if ($status !== LibraryReservationStatus::Collected) {
                $reservation->library_copy_id = null;
            }

            $reservation->save();

            $this->auditor->record(
                AuditAction::LibraryReservationClosed,
                $reservation,
                ['title' => $title?->title, 'ended_as' => $status->value],
                $actor,
                $reservation->school_id,
            );

            if ($title !== null && $status !== LibraryReservationStatus::Collected) {
                $this->hold->holdWhateverIsFree($title, $actor, $reservation->school_id);
            }

            return $reservation;
        });
    }
}
