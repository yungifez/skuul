<?php

namespace App\Actions\Academic;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\InstructionalModelSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RollForwardAcademicYearSetup
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Show the setup that can be copied without writing anything.
     *
     * @return array{
     *     items: list<array{key: string, title: string, description: string, details: list<string>, count: int, will_create: bool}>,
     *     create_count: int
     * }
     */
    public function preview(AcademicYear $source, AcademicYear $target): array
    {
        $this->validateYears($source, $target);

        $sourceSetting = $this->instructionalModelSetting($source);
        $targetHasSetting = $this->instructionalModelSetting($target) !== null;
        $sourcePeriods = $this->periods($source);
        $targetHasPeriods = $target->academicPeriods()->exists();
        $yearOffset = $target->start_year - $source->start_year;

        $items = [
            [
                'key' => 'instructional_model',
                'title' => 'Teaching approach',
                'description' => 'Use the same learner grouping approach in the new school year.',
                'details' => $sourceSetting === null
                    ? ['The previous year has no saved teaching approach.']
                    : [$sourceSetting->model->label()],
                'count' => $sourceSetting !== null && !$targetHasSetting ? 1 : 0,
                'will_create' => $sourceSetting !== null && !$targetHasSetting,
            ],
            [
                'key' => 'academic_periods',
                'title' => 'Reporting periods',
                'description' => 'Copy the previous year’s period structure with dates moved to the new year.',
                'details' => $sourcePeriods->isEmpty()
                    ? ['The previous year has no reporting periods.']
                    : $sourcePeriods
                        ->map(fn (AcademicPeriod $period): string => $this->periodDetail($period, $yearOffset))
                        ->values()
                        ->all(),
                'count' => $sourcePeriods->isNotEmpty() && !$targetHasPeriods ? $sourcePeriods->count() : 0,
                'will_create' => $sourcePeriods->isNotEmpty() && !$targetHasPeriods,
            ],
        ];

        return [
            'items' => $items,
            'create_count' => array_sum(array_column($items, 'count')),
        ];
    }

    /**
     * Copy the reusable setup after the person has reviewed the preview.
     *
     * @return array{instructional_model: bool, academic_periods: int}
     */
    public function rollForward(AcademicYear $source, AcademicYear $target, ?User $actor = null): array
    {
        $this->validateYears($source, $target);

        return DB::transaction(function () use ($source, $target, $actor): array {
            $target = AcademicYear::inSchool()->whereKey($target->id)->lockForUpdate()->firstOrFail();
            $createdInstructionalModel = false;
            $createdPeriods = 0;

            if ($this->instructionalModelSetting($target) === null) {
                $sourceSetting = $this->instructionalModelSetting($source);

                if ($sourceSetting !== null) {
                    InstructionalModelSetting::create([
                        'school_id' => $target->school_id,
                        'academic_year_id' => $target->id,
                        'model' => $sourceSetting->model,
                        'updated_by' => $actor === null ? auth()->id() : $actor->id,
                    ]);
                    $createdInstructionalModel = true;
                }
            }

            if (!$target->academicPeriods()->exists()) {
                $createdPeriods = $this->copyPeriods($source, $target);
            }

            if ($createdInstructionalModel || $createdPeriods > 0) {
                $this->auditor->record(
                    AuditAction::AcademicYearSetupRolledForward,
                    $target,
                    [
                        'source_academic_year_id' => $source->id,
                        'target_academic_year_id' => $target->id,
                        'instructional_model_created' => $createdInstructionalModel,
                        'academic_periods_created' => $createdPeriods,
                    ],
                    $actor,
                );
            }

            return [
                'instructional_model' => $createdInstructionalModel,
                'academic_periods' => $createdPeriods,
            ];
        });
    }

    private function copyPeriods(AcademicYear $source, AcademicYear $target): int
    {
        $periods = $this->periods($source);
        $sourceToTarget = [];
        $remaining = $periods->values();
        $created = 0;
        $yearOffset = $target->start_year - $source->start_year;

        while ($remaining->isNotEmpty()) {
            $progress = false;

            foreach ($remaining as $period) {
                if ($period->parent_id !== null && !isset($sourceToTarget[$period->parent_id])) {
                    continue;
                }

                $copy = AcademicPeriod::create([
                    'school_id' => $target->school_id,
                    'academic_year_id' => $target->id,
                    'parent_id' => $period->parent_id === null ? null : $sourceToTarget[$period->parent_id],
                    'name' => $period->name,
                    'label' => $period->label,
                    'type' => $period->type,
                    'position' => $period->position,
                    'starts_on' => $this->shiftDate($period->starts_on, $yearOffset),
                    'ends_on' => $this->shiftDate($period->ends_on, $yearOffset),
                    'status' => AcademicPeriodStatus::Draft,
                ]);

                $sourceToTarget[$period->id] = $copy->id;
                $remaining = $remaining->reject(fn (AcademicPeriod $remainingPeriod): bool => $remainingPeriod->id === $period->id)->values();
                $created++;
                $progress = true;
            }

            if (!$progress) {
                throw new InvalidValueException('The previous school year has an invalid reporting-period structure.');
            }
        }

        return $created;
    }

    /**
     * @return Collection<int, AcademicPeriod>
     */
    private function periods(AcademicYear $academicYear): Collection
    {
        return $academicYear->academicPeriods()
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    private function instructionalModelSetting(AcademicYear $academicYear): ?InstructionalModelSetting
    {
        return InstructionalModelSetting::query()
            ->where('school_id', $academicYear->school_id)
            ->where('academic_year_id', $academicYear->id)
            ->first();
    }

    private function shiftDate(?Carbon $date, int $yearOffset): ?Carbon
    {
        return $date?->copy()->addYears($yearOffset);
    }

    private function periodDetail(AcademicPeriod $period, int $yearOffset): string
    {
        $startsOn = $this->shiftDate($period->starts_on, $yearOffset);
        $endsOn = $this->shiftDate($period->ends_on, $yearOffset);

        if ($startsOn === null || $endsOn === null) {
            return $period->displayName.' (dates to review)';
        }

        return sprintf(
            '%s (%s – %s)',
            $period->displayName,
            $startsOn->format('M j, Y'),
            $endsOn->format('M j, Y'),
        );
    }

    private function validateYears(AcademicYear $source, AcademicYear $target): void
    {
        if ($source->school_id !== $target->school_id) {
            throw new InvalidValueException('Academic years from different schools cannot share setup.');
        }

        if ($source->id === $target->id) {
            throw new InvalidValueException('Choose a different academic year to receive the setup.');
        }

        if ($target->status->isFrozen()) {
            throw new InvalidValueException('The target academic year is closed.');
        }
    }
}
