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
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaveAcademicCalendar
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @param  array<int, array{name: string, type: string, starts_on: string, ends_on: string}>  $periods
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
                $this->removeDraftPeriods($academicYear);
            }

            foreach ($preparedPeriods as $position => $period) {
                $academicYear->academicPeriods()->create([
                    'school_id' => $school->id,
                    'name' => $period['name'],
                    'type' => $period['type'],
                    'position' => $position + 1,
                    'starts_on' => $period['starts_on'],
                    'ends_on' => $period['ends_on'],
                    'status' => AcademicPeriodStatus::Draft,
                ]);
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
     * @param  array<int, array{name: string, type: string, starts_on: string, ends_on: string}>  $periods
     * @return array<int, array{name: string, type: AcademicPeriodType, starts_on: Carbon, ends_on: Carbon}>
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

    private function removeDraftPeriods(AcademicYear $academicYear): void
    {
        AcademicPeriod::query()
            ->where('academic_year_id', $academicYear->id)
            ->whereNotNull('parent_id')
            ->delete();

        $academicYear->academicPeriods()->delete();
    }
}
