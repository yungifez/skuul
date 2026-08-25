<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Join the queue for a title.
 *
 * A reservation is for a title, not for one copy: any copy of it will do. When
 * a copy is free the reservation is ready at once and the copy is kept behind
 * the desk, so the person who asked first is not beaten to the shelf.
 */
class ReserveTitle
{
    public function __construct(
        private HoldCopyForNextInQueue $hold,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Put the borrower in the queue for the title.
     *
     * @throws InvalidValueException when the borrower cannot reserve this title
     */
    public function reserve(
        LibraryTitle $title,
        User $borrower,
        ?User $actor = null,
        ?int $schoolId = null,
    ): LibraryReservation {
        $schoolId ??= current_school_id();

        return DB::transaction(function () use ($title, $borrower, $actor, $schoolId): LibraryReservation {
            $title = LibraryTitle::forSchool($schoolId)
                ->whereHas('copies', fn ($query) => $query->where('school_id', $schoolId))
                ->lockForUpdate()
                ->findOrFail($title->getKey());

            $this->refuseWhatCannotBeReserved($title, $borrower, $schoolId);

            $reservation = LibraryReservation::create([
                'school_id' => $schoolId,
                'library_title_id' => $title->id,
                'user_id' => $borrower->id,
                'reserved_on' => now()->toDateString(),
                'created_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::LibraryReservationMade,
                $reservation,
                ['title' => $title->title, 'borrower' => $borrower->name],
                $actor,
                $schoolId,
            );

            // A copy already on the shelf is kept back at once, so the queue
            // and the shelf say the same thing from the first moment.
            $this->hold->holdWhateverIsFree($title, $actor, $schoolId);

            return $reservation->refresh();
        });
    }

    /**
     * Refuse a reservation the library should not take.
     *
     * @throws InvalidValueException
     */
    private function refuseWhatCannotBeReserved(LibraryTitle $title, User $borrower, int $schoolId): void
    {
        if (!$borrower->belongsToSchool($schoolId)) {
            throw new InvalidValueException('This person does not belong to the campus that owns the title.');
        }

        $already = LibraryReservation::query()
            ->where('school_id', $schoolId)
            ->where('library_title_id', $title->id)
            ->where('user_id', $borrower->id)
            ->stillGoing()
            ->exists();

        if ($already) {
            throw new InvalidValueException('This person is already in the queue for this title.');
        }

        $holdsOne = LibraryLoan::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $borrower->id)
            ->open()
            ->whereIn('library_copy_id', LibraryCopy::query()->select('id')->where('library_title_id', $title->id))
            ->exists();

        if ($holdsOne) {
            throw new InvalidValueException('This person already has a copy of this title.');
        }

        $limit = LibraryLendingRules::forSchool($schoolId)->limitFor($borrower);

        $waiting = LibraryReservation::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $borrower->id)
            ->stillGoing()
            ->count();

        if ($waiting >= $limit) {
            throw new InvalidValueException(
                "This person is already waiting for {$waiting} titles, which is as many as this library lends at once."
            );
        }
    }
}
