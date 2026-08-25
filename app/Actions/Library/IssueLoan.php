<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Hand a copy to somebody and say when it is due back.
 *
 * Two people at the desk must never lend the same copy twice, so the copy is
 * locked while the loan is written.
 */
class IssueLoan
{
    public function __construct(
        private CloseReservation $closeReservation,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Issue the copy.
     *
     * @throws InvalidValueException when the copy or the borrower cannot take the loan
     */
    public function issue(
        LibraryCopy $copy,
        User $borrower,
        ?User $actor = null,
        ?Carbon $issuedOn = null,
    ): LibraryLoan {
        return DB::transaction(function () use ($copy, $borrower, $actor, $issuedOn): LibraryLoan {
            $copy = LibraryCopy::query()->lockForUpdate()->with('title')->findOrFail($copy->getKey());
            $policy = LibraryLendingRules::forSchool($copy->school_id);

            $held = $this->heldCopyReservation($copy);

            $this->refuseWhatCannotBeLent($copy, $borrower, $policy);
            $this->refuseACopyHeldForSomebodyElse($held, $borrower);

            $issuedOn ??= now();

            $loan = LibraryLoan::create([
                'school_id' => $copy->school_id,
                'library_copy_id' => $copy->id,
                'user_id' => $borrower->id,
                'issued_on' => $issuedOn,
                'due_on' => $issuedOn->copy()->addDays($policy->loan_days),
                'issued_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            // The person the copy was waiting for has come for it, so their
            // place in the queue is done with.
            if ($held !== null) {
                $this->closeReservation->collected($held, $actor);
            }

            $this->auditor->record(
                AuditAction::LibraryLoanIssued,
                $loan,
                [
                    'title' => $copy->title?->title,
                    'barcode' => $copy->barcode,
                    'borrower' => $borrower->name,
                    'due_on' => $loan->due_on->toDateString(),
                ],
                $actor,
                $copy->school_id,
            );

            return $loan;
        });
    }

    /**
     * Get the reservation this copy is being kept behind the desk for.
     */
    private function heldCopyReservation(LibraryCopy $copy): ?LibraryReservation
    {
        return LibraryReservation::query()
            ->where('library_copy_id', $copy->id)
            ->where('status', LibraryReservationStatus::Ready->value)
            ->first();
    }

    /**
     * Refuse a copy that is waiting behind the desk for another person.
     *
     * @throws InvalidValueException
     */
    private function refuseACopyHeldForSomebodyElse(?LibraryReservation $held, User $borrower): void
    {
        if ($held !== null && $held->user_id !== $borrower->id) {
            throw new InvalidValueException(
                'This copy is being kept for somebody else who reserved it. Lend another copy.'
            );
        }
    }

    /**
     * Refuse anything the library should not lend.
     *
     * @throws InvalidValueException
     */
    private function refuseWhatCannotBeLent(LibraryCopy $copy, User $borrower, LibraryLendingRules $policy): void
    {
        if (!$borrower->belongsToSchool($copy->school_id)) {
            throw new InvalidValueException('This person does not belong to the campus that owns the copy.');
        }

        if (!$copy->status->canBeLent()) {
            throw new InvalidValueException("This copy is not on the shelf: {$copy->status->label()}.");
        }

        if ($copy->isOut()) {
            throw new InvalidValueException('Somebody already has this copy.');
        }

        $held = LibraryLoan::query()
            ->where('school_id', $copy->school_id)
            ->where('user_id', $borrower->id)
            ->open()
            ->count();

        if ($held >= $policy->limitFor($borrower)) {
            throw new InvalidValueException(
                "This person already has {$held} items, which is all this library lends at once.",
            );
        }
    }
}
