<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\BoardingRollEntryStatus;
use App\Enums\BoardingRollType;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingRoll;
use App\Models\BoardingRollEntry;
use App\Models\Dormitory;
use App\Models\User;
use App\Services\Boarding\BoardingRoster;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Start one boarding roll and snapshot the boarders who must be checked.
 */
class StartBoardingRoll
{
    public function __construct(private BoardingRoster $roster, private RecordAuditEvent $auditor) {}

    /**
     * Start a roll, or return the existing roll when the action is retried.
     *
     * @throws InvalidValueException when the house has no current boarders
     */
    public function start(
        Dormitory $dormitory,
        BoardingRollType $type,
        CarbonInterface|string|null $date = null,
        ?User $actor = null,
    ): BoardingRoll {
        $takenOn = Carbon::parse($date ?? now())->toDateString();

        return DB::transaction(function () use ($dormitory, $type, $takenOn, $actor): BoardingRoll {
            $roll = BoardingRoll::query()
                ->where('school_id', $dormitory->school_id)
                ->where('dormitory_id', $dormitory->id)
                ->where('type', $type->value)
                ->whereDate('taken_on', $takenOn)
                ->lockForUpdate()
                ->first();

            if ($roll !== null) {
                return $roll;
            }

            $boarders = $this->roster->inDormitory($dormitory);

            if ($boarders->isEmpty()) {
                throw new InvalidValueException('This house has no current boarders to check.');
            }

            $away = $this->roster->awayFrom($dormitory, $takenOn)->keyBy('student_record_id');
            $roll = BoardingRoll::create([
                'school_id' => $dormitory->school_id,
                'dormitory_id' => $dormitory->id,
                'type' => $type,
                'taken_on' => $takenOn,
                'started_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            foreach ($boarders as $place) {
                $leave = $away->get($place->student_record_id);

                BoardingRollEntry::create([
                    'school_id' => $roll->school_id,
                    'boarding_roll_id' => $roll->id,
                    'student_record_id' => $place->student_record_id,
                    'status' => $leave === null ? BoardingRollEntryStatus::NotRecorded : BoardingRollEntryStatus::Excused,
                    'location' => $leave?->destination,
                    'note' => $leave === null ? null : 'Approved overnight leave.',
                ]);
            }

            $this->auditor->record(
                AuditAction::BoardingRollStarted,
                $roll,
                ['type' => $type->value, 'taken_on' => $takenOn, 'entries' => $boarders->count()],
                $actor,
                $roll->school_id,
            );

            return $roll;
        });
    }
}
