<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LibraryCopyStatus;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Take a copy out of the library for good.
 *
 * The copy is kept, because its loans are the library's history. A person the
 * copy was held for keeps their place at the front of the queue, and gets the
 * next free copy of the title instead.
 */
class WithdrawLibraryCopy
{
    public function __construct(
        private HoldCopyForNextInQueue $hold,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * @throws InvalidValueException when somebody has the copy or it is gone already
     */
    public function withdraw(LibraryCopy $copy, ?User $actor = null): LibraryCopy
    {
        return DB::transaction(function () use ($copy, $actor): LibraryCopy {
            $copy = LibraryCopy::query()->lockForUpdate()->with('title')->findOrFail($copy->getKey());

            if (!$copy->status->isHeld()) {
                throw new InvalidValueException('This copy is already out of the library.');
            }

            if (LibraryLoan::query()->open()->where('library_copy_id', $copy->id)->exists()) {
                throw new InvalidValueException('Somebody has this copy. Take it back first.');
            }

            $copy->status = LibraryCopyStatus::Withdrawn;
            $copy->save();

            $held = LibraryReservation::query()
                ->where('library_copy_id', $copy->id)
                ->where('status', LibraryReservationStatus::Ready->value)
                ->lockForUpdate()
                ->first();

            if ($held !== null) {
                $held->status = LibraryReservationStatus::Waiting;
                $held->library_copy_id = null;
                $held->ready_on = null;
                $held->holds_until = null;
                $held->save();
            }

            $this->auditor->record(
                AuditAction::LibraryCopyWithdrawn,
                $copy,
                ['title' => $copy->title?->title, 'barcode' => $copy->barcode, 'was_held_for' => $held?->user_id],
                $actor,
                $copy->school_id,
            );

            if ($held !== null && $copy->title !== null) {
                $this->hold->holdWhateverIsFree($copy->title, $actor, $copy->school_id);
            }

            return $copy;
        });
    }
}
