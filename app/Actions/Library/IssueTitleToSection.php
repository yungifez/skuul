<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Lend one title to every attending learner in a home section.
 *
 * The operation is all-or-nothing. A class set that cannot be completed, or
 * one learner who cannot take the loan, leaves the shelf unchanged. A learner
 * who already has a copy of the title is passed over, so lending the set a
 * second time never hands anybody a second copy.
 */
class IssueTitleToSection
{
    public function __construct(
        private IssueLoan $issue,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Issue one copy to every attending learner in the section.
     *
     * @return Collection<int, LibraryLoan>
     *
     * @throws InvalidValueException when the set cannot be completed
     */
    public function issue(
        AcademicCycleSection $section,
        LibraryTitle $title,
        ?User $actor = null,
    ): Collection {
        return DB::transaction(function () use ($section, $title, $actor): Collection {
            $title = LibraryTitle::forSchool($section->school_id)
                ->whereHas('copies', fn ($query) => $query->where('school_id', $section->school_id))
                ->find($title->id);

            if ($title === null) {
                throw new InvalidValueException('This title is not available on the section\'s campus.');
            }

            $learners = StudentRecord::query()
                ->where('school_id', $section->school_id)
                ->where('academic_cycle_section_id', $section->id)
                ->attending()
                ->whereNotNull('user_id')
                ->with('user')
                ->orderBy('id')
                ->get();

            if ($learners->isEmpty()) {
                throw new InvalidValueException('This section has no attending learners.');
            }

            $copies = LibraryCopy::query()
                ->where('school_id', $section->school_id)
                ->where('library_title_id', $title->id)
                ->available()
                ->whereNotIn('id', LibraryReservation::query()
                    ->select('library_copy_id')
                    ->whereNotNull('library_copy_id')
                    ->where('status', LibraryReservationStatus::Ready->value))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            // Read after the copies are locked, and as a locking read, so a
            // set lent a moment earlier is seen here.
            $alreadyHolding = LibraryLoan::query()
                ->open()
                ->whereIn('user_id', $learners->pluck('user_id'))
                ->whereHas('copy', fn ($query) => $query->where('library_title_id', $title->id))
                ->sharedLock()
                ->pluck('user_id')
                ->all();

            $learners = $learners->reject(fn (StudentRecord $learner): bool => in_array($learner->user_id, $alreadyHolding, true))->values();

            if ($learners->isEmpty()) {
                throw new InvalidValueException('Every attending learner in this section already has a copy of this title.');
            }

            if ($copies->count() < $learners->count()) {
                throw new InvalidValueException(
                    "There are only {$copies->count()} available copies for {$learners->count()} learners."
                );
            }

            $loans = collect();

            foreach ($learners as $index => $learner) {
                $borrower = $learner->user;

                if ($borrower === null) {
                    throw new InvalidValueException('Every attending learner must have an account before lending.');
                }

                $loans->push($this->issue->issue($copies[$index], $borrower, $actor));
            }

            $this->auditor->record(
                AuditAction::LibrarySectionLoansIssued,
                $section,
                [
                    'title' => $title->title,
                    'learners' => $learners->count(),
                    'loans' => $loans->pluck('id')->all(),
                ],
                $actor,
                $section->school_id,
            );

            return $loans;
        });
    }
}
