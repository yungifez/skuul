<?php

namespace App\Services\Gradebook;

use App\Enums\GradeAggregation;
use App\Models\GradeCategory;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\StudentRecord;
use App\Models\Subject;
use Illuminate\Support\Collection;

/**
 * Work out what a student scored in a subject.
 *
 * Items can have different maximums, so every item is first turned into a
 * share of its own maximum. Categories are then put together the way each one
 * says. Excused work leaves the total alone; missing work counts as nothing.
 */
class GradebookCalculator
{
    /**
     * Get the result of one enrollment in one subject, as a percentage.
     *
     * @return array{percentage: float|null, points: float, max_points: float, items: array<int, array{item_id: int, name: string, state: string, points: float|null, max_points: float|null}>}
     */
    public function calculate(Subject $subject, StudentRecord $enrollment, ?int $academicYearId = null, ?int $academicPeriodId = null): array
    {
        $academicYearId ??= current_academic_year_id();

        $items = GradeItem::query()
            ->forSubject($subject)
            ->when($academicYearId !== null, fn ($query) => $query->where('academic_year_id', $academicYearId))
            ->when($academicPeriodId !== null, fn ($query) => $query->where('academic_period_id', $academicPeriodId))
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $entries = GradeEntry::query()
            ->whereIn('grade_item_id', $items->pluck('id'))
            ->where('student_record_id', $enrollment->id)
            ->get()
            ->keyBy('grade_item_id');

        $rows = $this->rowsFor($items, $entries);

        $categories = GradeCategory::query()
            ->where('subject_id', $subject->id)
            ->when($academicYearId !== null, fn ($query) => $query->where('academic_year_id', $academicYearId))
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $percentage = $this->aggregate($items, $rows, $categories);

        return [
            'percentage' => $percentage,
            'points' => round(collect($rows)->sum(fn (array $row): float => $row['points'] ?? 0.0), 2),
            'max_points' => round(collect($rows)->sum(fn (array $row): float => $row['counts'] ? ($row['max_points'] ?? 0.0) : 0.0), 2),
            'items' => array_values(array_map(fn (array $row): array => [
                'item_id' => $row['item_id'],
                'name' => $row['name'],
                'state' => $row['state'],
                'points' => $row['points'],
                'max_points' => $row['max_points'],
            ], $rows)),
        ];
    }

    /**
     * Turn each item into one row that says what it contributes.
     *
     * @param  Collection<int, GradeItem>  $items
     * @param  Collection<int, GradeEntry>  $entries
     * @return array<int, array{item_id: int, name: string, state: string, points: float|null, max_points: float|null, weight: float, category_id: int|null, counts: bool, share: float|null}>
     */
    private function rowsFor(Collection $items, Collection $entries): array
    {
        $rows = [];

        foreach ($items as $item) {
            $entry = $entries->get($item->id);
            $state = $entry?->state;
            $counts = $item->type->carriesPoints()
                && $item->max_points !== null
                && $item->max_points > 0
                && ($state === null || $state->countsInTotal());

            $points = null;

            if ($counts) {
                // No entry yet, or work that was not done, is worth nothing.
                $points = $state !== null && $state->needsPoints() ? (float) ($entry->points ?? 0.0) : 0.0;
            }

            $rows[] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'state' => $state === null ? 'not_entered' : $state->value,
                'points' => $points,
                'max_points' => $item->max_points,
                'weight' => $item->weight,
                'category_id' => $item->grade_category_id,
                'counts' => $counts,
                'share' => $counts && $item->max_points > 0 ? $points / $item->max_points : null,
            ];
        }

        return $rows;
    }

    /**
     * Put the rows together into one percentage.
     *
     * @param  Collection<int, GradeItem>  $items
     * @param  array<int, array<string, mixed>>  $rows
     * @param  Collection<int, GradeCategory>  $categories
     */
    private function aggregate(Collection $items, array $rows, Collection $categories): ?float
    {
        $counting = array_values(array_filter($rows, fn (array $row): bool => $row['counts']));

        if ($counting === []) {
            return null;
        }

        // Items outside any category are put together as one weighted group.
        $groups = [];

        foreach ($counting as $row) {
            $groups[$row['category_id'] ?? 0][] = $row;
        }

        $groupResults = [];

        foreach ($groups as $categoryId => $groupRows) {
            $category = $categoryId === 0 ? null : $categories->firstWhere('id', $categoryId);
            $aggregation = $category === null ? GradeAggregation::WeightedMean : $category->aggregation;

            $groupResults[] = [
                'share' => $this->aggregateGroup($aggregation, $groupRows),
                'weight' => $category === null ? 1.0 : $category->weight,
            ];
        }

        $totalWeight = array_sum(array_column($groupResults, 'weight'));

        if ($totalWeight <= 0.0) {
            return null;
        }

        $share = 0.0;

        foreach ($groupResults as $result) {
            $share += $result['share'] * $result['weight'];
        }

        return round(($share / $totalWeight) * 100, 2);
    }

    /**
     * Put one group of rows together the way its category says.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function aggregateGroup(GradeAggregation $aggregation, array $rows): float
    {
        $shares = array_map(fn (array $row): float => (float) $row['share'], $rows);
        $weights = array_map(fn (array $row): float => (float) $row['weight'], $rows);

        return match ($aggregation) {
            GradeAggregation::SimpleMean => array_sum($shares) / count($shares),
            GradeAggregation::Highest => max($shares),
            GradeAggregation::Sum => $this->sumShare($rows),
            GradeAggregation::WeightedMean => array_sum($weights) > 0
                ? array_sum(array_map(fn (float $share, float $weight): float => $share * $weight, $shares, $weights)) / array_sum($weights)
                : 0.0,
        };
    }

    /**
     * Add the points of a group and divide by the total maximum.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function sumShare(array $rows): float
    {
        $points = array_sum(array_map(fn (array $row): float => (float) $row['points'], $rows));
        $max = array_sum(array_map(fn (array $row): float => (float) $row['max_points'], $rows));

        return $max > 0 ? $points / $max : 0.0;
    }
}
