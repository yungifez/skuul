<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LibraryCopyStatus;
use App\Enums\LibraryReservationStatus;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\User;

/**
 * Keep a copy behind the desk for whoever is next in the queue.
 *
 * This runs when a copy comes back and when somebody joins a queue for a title
 * that has a copy on the shelf. Either way the rule is the same: the copy goes
 * to the person who has waited longest, and it waits for them for a few days.
 */
class HoldCopyForNextInQueue
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Hold a free copy of the title for the person at the front of the queue.
     *
     * Nothing happens when nobody is waiting or no copy is free.
     */
    public function holdWhateverIsFree(
        LibraryTitle $title,
        ?User $actor = null,
        ?int $schoolId = null,
    ): ?LibraryReservation {
        $schoolId ??= current_school_id();

        $next = LibraryReservation::query()
            ->where('school_id', $schoolId)
            ->where('library_title_id', $title->id)
            ->where('status', LibraryReservationStatus::Waiting->value)
            ->orderBy('id')
            ->lockForUpdate()
            ->first();

        if ($next === null) {
            return null;
        }

        $copy = $this->freeCopyOf($title, $schoolId);

        return $copy === null ? null : $this->hold($next, $copy, $actor);
    }

    /**
     * Put one copy behind the desk for one reservation.
     */
    public function hold(LibraryReservation $reservation, LibraryCopy $copy, ?User $actor = null): LibraryReservation
    {
        $rules = LibraryLendingRules::forSchool($copy->school_id);

        $reservation->status = LibraryReservationStatus::Ready;
        $reservation->library_copy_id = $copy->id;
        $reservation->ready_on = now();
        $reservation->holds_until = now()->addDays($rules->hold_days);
        $reservation->save();

        $this->auditor->record(
            AuditAction::LibraryReservationReady,
            $reservation,
            [
                'title' => $copy->title?->title,
                'barcode' => $copy->barcode,
                'holds_until' => $reservation->holds_until->toDateString(),
            ],
            $actor,
            $copy->school_id,
        );

        return $reservation;
    }

    /**
     * Get a copy of the title that nobody has and nobody is holding.
     */
    private function freeCopyOf(LibraryTitle $title, int $schoolId): ?LibraryCopy
    {
        return LibraryCopy::query()
            ->where('school_id', $schoolId)
            ->where('library_title_id', $title->id)
            ->where('status', LibraryCopyStatus::OnShelf->value)
            ->with('title')
            ->whereNotIn('id', LibraryLoan::query()->select('library_copy_id')->open())
            ->whereNotIn('id', LibraryReservation::query()
                ->select('library_copy_id')
                ->whereNotNull('library_copy_id')
                ->where('status', LibraryReservationStatus::Ready->value))
            ->orderBy('id')
            ->lockForUpdate()
            ->first();
    }
}
