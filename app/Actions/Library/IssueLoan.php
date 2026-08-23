<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
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
    public function __construct(private RecordAuditEvent $auditor) {}

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

            $this->refuseWhatCannotBeLent($copy, $borrower, $policy);

            $issuedOn ??= now();

            $loan = LibraryLoan::create([
                'school_id' => $copy->school_id,
                'library_copy_id' => $copy->id,
                'user_id' => $borrower->id,
                'issued_on' => $issuedOn,
                'due_on' => $issuedOn->copy()->addDays($policy->loan_days),
                'issued_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

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
