<?php

namespace App\Actions\Academic;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaveAcademicCalendar
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @param  array<int, array{id?: int|null, name: string, type: string, starts_on: string, ends_on: string}>  $periods
     */
    public function save(
        School $school,
        Carbon $startsOn,
        Carbon $endsOn,
        array $periods,
        ?User $actor = null,
        ?AcademicYear $academicYear = null,
    ): AcademicYear {
        $startsOn = $startsOn->copy()->startOfDay();
        $endsOn = $endsOn->copy()->startOfDay();

        if ($endsOn->lt($startsOn)) {
            throw new InvalidValueException('A school calendar cannot end before it starts.');
        }

        if ($academicYear !== null) {
            if ($academicYear->school_id !== $school->id) {
                throw new InvalidValueException('That school calendar belongs to another school.');
            }

            if ($academicYear->status !== AcademicPeriodStatus::Draft) {
                throw new InvalidValueException('Publish a calendar only after its dates are final. Published calendars are changed from their detail page.');
            }
        }

        $preparedPeriods = $this->preparePeriods($periods, $startsOn, $endsOn);
        $this->refuseOverlap($school, $startsOn, $endsOn, $academicYear);

        return DB::transaction(function () use ($school, $startsOn, $endsOn, $preparedPeriods, $actor, $academicYear): AcademicYear {
            $values = [
                'start_year' => (int) $startsOn->format('Y'),
                'stop_year' => (int) $endsOn->format('Y'),
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'status' => AcademicPeriodStatus::Draft,
            ];

            $isNew = $academicYear === null;
            $academicYear ??= $school->academicYears()->create($values);

            if (!$isNew) {
                $academicYear->fill($values)->save();
            }

            $existingPeriods = $isNew
                ? collect()
                : $academicYear->academicPeriods()->get()->keyBy('id');
            $preparedIds = collect($preparedPeriods)
                ->pluck('id')
                ->filter()
                ->map(fn (int|string $id): int => (int) $id)
                ->all();
            $periodsToRemove = $existingPeriods
                ->filter(fn (AcademicPeriod $period): bool => !in_array($period->id, $preparedIds, true))
                ->keyBy('id');

            $this->refuseRemovingPeriodsWithTimetables($periodsToRemove);

            foreach ($preparedPeriods as $position => $period) {
                $periodModel = $period['id'] === null ? null : $existingPeriods->get($period['id']);

                if ($period['id'] !== null && $periodModel === null) {
                    throw new InvalidValueException('That reporting period does not belong to this school calendar.');
                }

                $attributes = [
                    'school_id' => $school->id,
                    'name' => $period['name'],
                    'type' => $period['type'],
                    'position' => $position + 1,
                    'starts_on' => $period['starts_on'],
                    'ends_on' => $period['ends_on'],
                ];

                if ($periodModel === null) {
                    $academicYear->academicPeriods()->create($attributes + [
                        'status' => AcademicPeriodStatus::Draft,
                    ]);
                } else {
                    $periodModel->fill($attributes)->save();
                }
            }

            foreach ($periodsToRemove as $period) {
                $period->delete();
            }

            $this->auditor->record(
                AuditAction::AcademicCalendarSaved,
                $academicYear,
                [
                    'created' => $isNew,
                    'starts_on' => $startsOn->toDateString(),
                    'ends_on' => $endsOn->toDateString(),
                    'period_count' => count($preparedPeriods),
                ],
                $actor,
            );

            return $academicYear->refresh();
        }, attempts: 3);
    }

    /**
     * @param  array<int, array{id?: int|null, name: string, type: string, starts_on: string, ends_on: string}>  $periods
     * @return array<int, array{id: int|null, name: string, type: AcademicPeriodType, starts_on: Carbon, ends_on: Carbon}>
     */
    private function preparePeriods(array $periods, Carbon $calendarStartsOn, Carbon $calendarEndsOn): array
    {
        if ($periods === []) {
            throw new InvalidValueException('Add at least one reporting period to the school calendar.');
        }

        $prepared = [];

        foreach ($periods as $period) {
            $startsOn = Carbon::parse($period['starts_on'])->startOfDay();
            $endsOn = Carbon::parse($period['ends_on'])->startOfDay();
            $type = AcademicPeriodType::from($period['type']);

            if (!$type->isPrimaryDivision() && $type !== AcademicPeriodType::Other) {
                throw new InvalidValueException('A school calendar can only use terms, semesters, trimesters, quarters, or custom reporting periods.');
            }

            if ($endsOn->lt($startsOn)) {
                throw new InvalidValueException("{$period['name']} cannot end before it starts.");
            }

            if ($startsOn->lt($calendarStartsOn) || $endsOn->gt($calendarEndsOn)) {
                throw new InvalidValueException("{$period['name']} must sit inside the school calendar dates.");
            }

            $prepared[] = [
                'id' => isset($period['id']) ? (int) $period['id'] : null,
                'name' => $period['name'],
                'type' => $type,
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
            ];
        }

        usort($prepared, fn (array $left, array $right): int => $left['starts_on']->getTimestamp() <=> $right['starts_on']->getTimestamp());

        foreach ($prepared as $position => $period) {
            if ($position > 0 && $period['starts_on']->lte($prepared[$position - 1]['ends_on'])) {
                throw new InvalidValueException("{$period['name']} overlaps {$prepared[$position - 1]['name']}.");
            }
        }

        return $prepared;
    }

    private function refuseOverlap(School $school, Carbon $startsOn, Carbon $endsOn, ?AcademicYear $ignore): void
    {
        $overlap = $school->academicYears()
            ->when($ignore !== null, fn ($query) => $query->whereKeyNot($ignore->id))
            ->whereNotNull('starts_on')
            ->whereNotNull('ends_on')
            ->whereDate('starts_on', '<=', $endsOn->toDateString())
            ->whereDate('ends_on', '>=', $startsOn->toDateString())
            ->first();

        if ($overlap !== null) {
            throw new InvalidValueException("This calendar overlaps {$overlap->name}.");
        }
    }

    /**
     * Do not let a calendar edit delete a period that still owns timetable data.
     *
     * Keeping the period row is what keeps its timetable, teaching assignments,
     * and historical references attached when only its dates change.
     *
     * @param  Collection<int, AcademicPeriod>  $periods
     *
     * @throws InvalidValueException
     */
    private function refuseRemovingPeriodsWithTimetables(Collection $periods): void
    {
        if ($periods->isEmpty()) {
            return;
        }

        $timetableCounts = Timetable::query()
            ->whereIn('academic_period_id', $periods->keys()->all())
            ->select('academic_period_id')
            ->selectRaw('count(*) as timetable_count')
            ->groupBy('academic_period_id')
            ->pluck('timetable_count', 'academic_period_id');

        foreach ($timetableCounts as $periodId => $count) {
            $period = $periods->get($periodId);

            throw new InvalidValueException(
                "{$period->displayName} has {$count} timetable(s). Keep this period or move its timetables before removing it."
            );
        }
    }
}
