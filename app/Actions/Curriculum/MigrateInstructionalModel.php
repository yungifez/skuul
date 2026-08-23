<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\InstructionalModelMigration;
use App\Models\InstructionalModelSetting;
use App\Models\User;
use App\Services\Curriculum\InstructionalModelResolver;
use Illuminate\Support\Facades\DB;

/**
 * Move a running cycle to another instructional model.
 *
 * `SetInstructionalModel` refuses a cycle that has started, because the answer
 * decides what staff are asked for and a quiet change would make the screens
 * disagree with the work already done. A campus that must change anyway comes
 * here: the move needs its own permission, a written reason, and it keeps a
 * record of what the cycle held at that moment.
 *
 * Nothing already recorded is rewritten. An offering whose roster the new
 * model would not offer stays exactly as it is and is counted in the record as
 * an exception the campus now carries.
 */
class MigrateInstructionalModel
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private InstructionalModelResolver $resolver,
    ) {}

    /**
     * Move the cycle, and record why.
     *
     * @throws InvalidValueException when the cycle cannot be moved this way
     */
    public function migrate(
        AcademicYear $academicYear,
        InstructionalModel $model,
        string $reason,
        ?User $actor = null,
    ): InstructionalModelMigration {
        $reason = trim($reason);
        $current = $this->resolver->for($academicYear);

        $this->refuseMoveThatDoesNotBelongHere($academicYear, $model, $current, $reason);

        $impact = $this->impactOf($academicYear, $model);

        return DB::transaction(function () use ($academicYear, $model, $current, $reason, $impact, $actor): InstructionalModelMigration {
            InstructionalModelSetting::updateOrCreate(
                [
                    'school_id' => $academicYear->school_id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'model' => $model,
                    'updated_by' => $actor === null ? auth()->id() : $actor->id,
                ],
            );

            $this->resolver->forget();

            $migration = InstructionalModelMigration::create([
                'school_id' => $academicYear->school_id,
                'academic_year_id' => $academicYear->id,
                'from_model' => $current,
                'to_model' => $model,
                'reason' => $reason,
                'impact' => $impact,
                'migrated_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::InstructionalModelMigrated,
                $migration,
                [
                    'from' => $current->value,
                    'to' => $model->value,
                    'reason' => $reason,
                    'school_id' => $academicYear->school_id,
                    'academic_year_id' => $academicYear->id,
                    'impact' => $impact,
                ],
                $actor,
            );

            return $migration;
        });
    }

    /**
     * Check if a running cycle may be moved at all.
     *
     * A finished cycle may not. Its records are read-only, and the model that
     * produced them is part of what they mean.
     */
    public function canBeMigrated(AcademicYear $academicYear): bool
    {
        return $academicYear->status->isOperational();
    }

    /**
     * Describe what the move would meet in this cycle.
     *
     * The counts are read before the move and kept with the record, so a
     * person reading the history later sees the cycle as it was, not as it
     * became.
     *
     * @return array{offerings: int, offerings_by_roster: array<string, int>, exceptions: int, exception_rosters: array<string, int>}
     */
    public function impactOf(AcademicYear $academicYear, InstructionalModel $model): array
    {
        $counts = CourseOffering::query()
            ->where('school_id', $academicYear->school_id)
            ->where('academic_year_id', $academicYear->id)
            ->selectRaw('roster_mode, count(*) as total')
            ->groupBy('roster_mode')
            ->pluck('total', 'roster_mode');

        $byRoster = [];
        $exceptionRosters = [];
        $exceptions = 0;

        foreach ($counts as $rosterMode => $total) {
            $total = (int) $total;
            $byRoster[(string) $rosterMode] = $total;

            $roster = RosterMode::tryFrom((string) $rosterMode);

            if ($roster !== null && !$model->allowsRosterMode($roster)) {
                $exceptionRosters[(string) $rosterMode] = $total;
                $exceptions += $total;
            }
        }

        return [
            'offerings' => array_sum($byRoster),
            'offerings_by_roster' => $byRoster,
            'exceptions' => $exceptions,
            'exception_rosters' => $exceptionRosters,
        ];
    }

    /**
     * Stop a move that belongs somewhere else, or nowhere.
     *
     * @throws InvalidValueException
     */
    private function refuseMoveThatDoesNotBelongHere(
        AcademicYear $academicYear,
        InstructionalModel $model,
        InstructionalModel $current,
        string $reason,
    ): void {
        if ($academicYear->status->isFrozen()) {
            throw new InvalidValueException(
                "{$academicYear->name} is finished, so how it was taught cannot change. Reopen the cycle first if it was closed by mistake."
            );
        }

        if (!$this->canBeMigrated($academicYear)) {
            throw new InvalidValueException(
                "{$academicYear->name} has not started yet, so choose its model on the teaching setup form instead."
            );
        }

        if ($current === $model) {
            throw new InvalidValueException("{$academicYear->name} already teaches with: {$model->label()}");
        }

        if ($reason === '') {
            throw new InvalidValueException('Say why this cycle is moving to another model.');
        }
    }
}
