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
        return $this->close($reservation, LibraryReservationStatus::Cancelled, $actor);
    }

    /**
     * Take off every reservation a borrower still has at one campus.
     *
     * A learner who moved on or left no longer comes to that library, so a
     * copy held for them would wait behind the desk until it ran out.
     *
     * @return int how many reservations were taken off
     */
    public function cancelEveryReservation(User $borrower, int $schoolId, ?User $actor = null): int
    {
        $reservations = LibraryReservation::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $borrower->id)
            ->stillGoing()
            ->get();

        foreach ($reservations as $reservation) {
            $this->cancel($reservation, $actor);
        }

        return $reservations->count();
    }

    /**
     * Give up on a hold nobody came for.
     *
     * @throws InvalidValueException when the copy is no longer behind the desk
     */
    public function expire(LibraryReservation $reservation, ?User $actor = null): LibraryReservation
    {
        return $this->close($reservation, LibraryReservationStatus::Expired, $actor);
    }

    /**
     * Write the ending and offer the copy to the next person.
     *
     * The reservation is read again under a lock, so a stale screen or the
     * nightly clean-up cannot end a reservation that was collected meanwhile.
     *
     * @throws InvalidValueException when the reservation has already ended
     */
    private function close(LibraryReservation $reservation, LibraryReservationStatus $status, ?User $actor): LibraryReservation
    {
        return DB::transaction(function () use ($reservation, $status, $actor): LibraryReservation {
            $reservation = LibraryReservation::query()->lockForUpdate()->findOrFail($reservation->getKey());

            if (!$reservation->isOpen()) {
                throw new InvalidValueException('This reservation has already ended.');
            }

            if ($status === LibraryReservationStatus::Expired && $reservation->status !== LibraryReservationStatus::Ready) {
                throw new InvalidValueException('Only a copy behind the desk can run out of time.');
            }

            $reservation->load('title');
            $title = $reservation->title;

            $reservation->status = $status;
            $reservation->closed_on = school_today($reservation->school_id);

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
