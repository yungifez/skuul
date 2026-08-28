<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RollForwardCourseOfferings
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Show which level-specific offerings can be copied into the target year.
     *
     * @return array{copies: Collection<int, array{offering: CourseOffering, period: AcademicPeriod, section_count: int}>, skips: Collection<int, CourseOffering>, problems: Collection<int, array{offering: CourseOffering, reason: string}>}
     */
    public function preview(AcademicYear $source, AcademicYear $target): array
    {
        $this->validateYears($source, $target);

        $plan = collect($this->plan($source, $target));

        return [
            'copies' => $plan->where('status', 'copy')->map(fn (array $item): array => [
                'offering' => $item['offering'],
                'period' => $item['period'],
                'section_count' => count($item['section_ids']),
            ])->values(),
            'skips' => $plan->where('status', 'skip')->pluck('offering')->values(),
            'problems' => $plan->where('status', 'problem')->map(fn (array $item): array => [
                'offering' => $item['offering'],
                'reason' => $item['reason'],
            ])->values(),
        ];
    }

    /**
     * Copy level-specific offerings as drafts. Learners and teachers stay in the source year.
     *
     * @return EloquentCollection<int, CourseOffering>
     */
    public function rollForward(AcademicYear $source, AcademicYear $target, ?User $actor = null): EloquentCollection
    {
        $this->validateYears($source, $target);

        return DB::transaction(function () use ($source, $target, $actor): EloquentCollection {
            $target = AcademicYear::inSchool()->whereKey($target->id)->lockForUpdate()->firstOrFail();
            $created = new EloquentCollection;

            foreach ($this->plan($source, $target) as $item) {
                if ($item['status'] !== 'copy') {
                    continue;
                }

                /** @var CourseOffering $sourceOffering */
                $sourceOffering = $item['offering'];
                $copy = CourseOffering::create([
                    'school_id' => $target->school_id,
                    'academic_year_id' => $target->id,
                    'academic_period_id' => $item['period']->id,
                    'subject_id' => $sourceOffering->subject_id,
                    'academic_level_id' => $sourceOffering->academic_level_id,
                    'roster_mode' => $sourceOffering->roster_mode,
                    'planned_periods_per_week' => $sourceOffering->planned_periods_per_week,
                    'capacity' => $sourceOffering->capacity,
                ]);
                $copy->cycleSections()->sync($item['section_ids']);
                $created->push($copy);

                $this->auditor->record(
                    AuditAction::CourseOfferingCreated,
                    $copy,
                    [
                        'source_academic_year_id' => $source->id,
                        'target_academic_year_id' => $target->id,
                        'rolled_forward' => true,
                    ],
                    $actor,
                );
            }

            return $created;
        });
    }

    /**
     * @return list<array{status: 'copy'|'skip'|'problem', offering: CourseOffering, period: AcademicPeriod|null, section_ids: list<int>, reason?: string}>
     */
    private function plan(AcademicYear $source, AcademicYear $target): array
    {
        $targetPeriods = $target->academicPeriods()
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('position')
            ->get();
        $periods = $this->periodMap(
            $source->academicPeriods()->orderByRaw('parent_id IS NOT NULL')->orderBy('position')->get(),
            $targetPeriods,
        );
        $targetSections = AcademicCycleSection::inSchool()
            ->where('academic_year_id', $target->id)
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->get()
            ->keyBy(fn (AcademicCycleSection $section): string => $section->academic_level_id.':'.$section->name);
        $existing = CourseOffering::inSchool()
            ->where('academic_year_id', $target->id)
            ->get()
            ->keyBy(fn (CourseOffering $offering): string => $offering->subject_id.':'.$offering->academic_period_id.':'.$offering->academic_level_id);

        return CourseOffering::inSchool()
            ->with(['subject', 'academicPeriod', 'academicLevel', 'cycleSections'])
            ->where('academic_year_id', $source->id)
            ->get()
            ->map(function (CourseOffering $offering) use ($periods, $targetSections, $existing): array {
                $period = $periods[$offering->academic_period_id] ?? null;

                if (!$period instanceof AcademicPeriod) {
                    return ['status' => 'problem', 'offering' => $offering, 'period' => null, 'section_ids' => [], 'reason' => 'The matching reporting period does not exist in the new year.'];
                }

                if ($existing->has($offering->subject_id.':'.$period->id.':'.$offering->academic_level_id)) {
                    return ['status' => 'skip', 'offering' => $offering, 'period' => $period, 'section_ids' => []];
                }

                $sectionIds = $offering->roster_mode->usesHomeSections()
                    ? $offering->cycleSections
                        ->map(fn (AcademicCycleSection $section): ?int => $targetSections->get($section->academic_level_id.':'.$section->name)?->id)
                        ->filter()
                        ->map(fn (int $sectionId): int => $sectionId)
                        ->values()
                        ->all()
                    : [];

                if ($offering->roster_mode->usesHomeSections() && count($sectionIds) !== $offering->cycleSections->count()) {
                    return ['status' => 'problem', 'offering' => $offering, 'period' => $period, 'section_ids' => [], 'reason' => 'Create the matching sections in the new year first.'];
                }

                return ['status' => 'copy', 'offering' => $offering, 'period' => $period, 'section_ids' => $sectionIds];
            })
            ->values()
            ->all();
    }

    /**
     * @param  EloquentCollection<int, AcademicPeriod>  $sourcePeriods
     * @param  EloquentCollection<int, AcademicPeriod>  $targetPeriods
     * @return array<int, AcademicPeriod>
     */
    private function periodMap(EloquentCollection $sourcePeriods, EloquentCollection $targetPeriods): array
    {
        $map = [];

        foreach ($sourcePeriods as $sourcePeriod) {
            $targetParentId = $sourcePeriod->parent_id === null
                ? null
                : ($map[$sourcePeriod->parent_id]->id ?? null);
            $targetPeriod = $targetPeriods->first(fn (AcademicPeriod $candidate): bool => $candidate->name === $sourcePeriod->name
                && $candidate->label === $sourcePeriod->label
                && $candidate->type === $sourcePeriod->type
                && $candidate->position === $sourcePeriod->position
                && $candidate->parent_id === $targetParentId);

            if ($targetPeriod instanceof AcademicPeriod) {
                $map[$sourcePeriod->id] = $targetPeriod;
            }
        }

        return $map;
    }

    private function validateYears(AcademicYear $source, AcademicYear $target): void
    {
        if ($source->school_id !== $target->school_id) {
            throw new InvalidValueException('Academic years from different schools cannot share course offerings.');
        }

        if ($source->id === $target->id) {
            throw new InvalidValueException('Choose a different academic year to receive the course offerings.');
        }

        if ($target->isClosed()) {
            throw new InvalidValueException('Reopen the target academic year before copying course offerings.');
        }
    }
}
