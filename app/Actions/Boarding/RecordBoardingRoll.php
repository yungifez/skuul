<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\BoardingRollEntryStatus;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingRoll;
use App\Models\BoardingRollEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Save the staff answers for a boarding roll.
 */
class RecordBoardingRoll
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Save the answers and optionally finish the roll.
     *
     * @param  array<int, array{id: int, status: string, location?: string|null, note?: string|null}>  $entries
     *
     * @throws InvalidValueException when a completed roll is edited or an answer is missing
     */
    public function record(BoardingRoll $roll, array $entries, bool $complete = false, ?User $actor = null): BoardingRoll
    {
        if ($roll->isComplete()) {
            throw new InvalidValueException('A completed roll cannot be changed.');
        }

        return DB::transaction(function () use ($roll, $entries, $complete, $actor): BoardingRoll {
            foreach ($entries as $entry) {
                $rollEntry = BoardingRollEntry::query()
                    ->where('school_id', $roll->school_id)
                    ->where('boarding_roll_id', $roll->id)
                    ->findOrFail($entry['id']);

                $rollEntry->update([
                    'status' => BoardingRollEntryStatus::from($entry['status']),
                    'location' => $entry['location'] ?? null,
                    'note' => $entry['note'] ?? null,
                    'recorded_by' => $actor === null ? auth()->id() : $actor->id,
                    'recorded_at' => now(),
                ]);
            }

            if ($complete) {
                $hasMissing = $roll->entries()
                    ->where('status', BoardingRollEntryStatus::NotRecorded)
                    ->exists();

                if ($hasMissing) {
                    throw new InvalidValueException('Record every boarder before completing the roll.');
                }

                $roll->update([
                    'completed_at' => now(),
                    'completed_by' => $actor === null ? auth()->id() : $actor->id,
                ]);
            }

            $this->auditor->record(
                AuditAction::BoardingRollRecorded,
                $roll,
                ['entries' => count($entries), 'completed' => $complete],
                $actor,
                $roll->school_id,
            );

            return $roll->refresh();
        });
    }
}
