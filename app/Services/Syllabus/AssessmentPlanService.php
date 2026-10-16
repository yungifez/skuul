<?php

namespace App\Services\Syllabus;

use App\Enums\GradeAggregation;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use Illuminate\Support\Collection;

/**
 * Show how a course offering is assessed, and which planned topics each assessment tests.
 *
 * The weights come from the gradebook, so the plan and the results cannot disagree.
 */
class AssessmentPlanService
{
    /**
     * Get the gradebook of the syllabus's offering as an assessment plan.
     *
     * A group is a category, or the items outside any category. Its share is
     * its part of the final grade, as GradebookCalculator weighs it. An item's
     * share is its part of the final grade, or null when its category keeps
     * only the highest result.
     *
     * @return list<array{name: string, aggregation: GradeAggregation, share: float, items: list<array{item: GradeItem, share: float|null, week: int|null, early_topics: list<string>}>}>
     */
    public function plan(Syllabus $syllabus): array
    {
        $items = GradeItem::query()
            ->forCourseOffering($syllabus->course_offering_id)
            ->with(['syllabusTopics' => fn ($topics) => $topics->where('syllabus_id', $syllabus->id)->orderBy('week')->orderBy('position')])
            ->orderBy('position')
            ->orderBy('id')
            ->get();
        $categories = GradeCategory::query()
            ->where('course_offering_id', $syllabus->course_offering_id)
            ->orderBy('position')
            ->get()
            ->keyBy('id');

        $groups = $items->groupBy(fn (GradeItem $item): int => $item->grade_category_id ?? 0);
        $counted = $groups->filter(fn (Collection $group): bool => $group->contains(fn (GradeItem $item): bool => $item->type->carriesPoints()));
        $totalWeight = $counted->keys()->sum(fn (int $categoryId): float => $categoryId === 0 ? 1.0 : (float) $categories->get($categoryId)?->weight);

        $plan = [];

        foreach ($groups as $categoryId => $group) {
            $category = $categoryId === 0 ? null : $categories->get($categoryId);
            $aggregation = $category === null ? GradeAggregation::WeightedMean : $category->aggregation;
            $groupShare = $counted->has($categoryId) && $totalWeight > 0
                ? ($category === null ? 1.0 : $category->weight) / $totalWeight
                : 0.0;

            $plan[] = [
                'name' => $category === null ? 'Other assessments' : $category->name,
                'aggregation' => $aggregation,
                'share' => round($groupShare * 100, 1),
                'items' => $this->planItems($syllabus, $group, $aggregation, $groupShare),
            ];
        }

        return $plan;
    }

    /**
     * Get the planned topics that no assessment tests.
     *
     * @return Collection<int, SyllabusTopic>
     */
    public function untestedTopics(Syllabus $syllabus): Collection
    {
        return $syllabus->topics()->whereDoesntHave('gradeItems')->get();
    }

    /**
     * Set the published topics an assessment tests.
     *
     * @param  list<int>  $topicIds
     */
    public function tagTopics(Syllabus $syllabus, GradeItem $item, array $topicIds): void
    {
        if ($syllabus->fresh()?->status !== SyllabusStatus::Published) {
            throw new InvalidValueException('Assessments are linked to the published syllabus only.');
        }

        if ($item->course_offering_id !== $syllabus->course_offering_id) {
            throw new InvalidValueException('That assessment belongs to another course offering.');
        }

        $topicIds = array_values(array_unique($topicIds));

        if ($syllabus->topics()->whereKey($topicIds)->count() !== count($topicIds)) {
            throw new InvalidValueException('Choose topics from this syllabus only.');
        }

        // Tags on other revisions of the syllabus stay as they are.
        $item->syllabusTopics()->detach($syllabus->topics()->whereKeyNot($topicIds)->pluck('syllabus_topics.id')->all());
        $item->syllabusTopics()->syncWithoutDetaching($topicIds);
    }

    /**
     * @param  Collection<int, GradeItem>  $group
     * @return list<array{item: GradeItem, share: float|null, week: int|null, early_topics: list<string>}>
     */
    private function planItems(Syllabus $syllabus, Collection $group, GradeAggregation $aggregation, float $groupShare): array
    {
        $counting = $group->filter(fn (GradeItem $item): bool => $item->type->carriesPoints());
        $divisor = match ($aggregation) {
            GradeAggregation::WeightedMean => (float) $counting->sum('weight'),
            GradeAggregation::SimpleMean => (float) $counting->count(),
            GradeAggregation::Sum => (float) $counting->sum(fn (GradeItem $item): float => $item->max_points ?? 0.0),
            GradeAggregation::Highest => 0.0,
        };

        $rows = [];

        foreach ($group as $item) {
            $part = match ($aggregation) {
                GradeAggregation::WeightedMean => $item->weight,
                GradeAggregation::SimpleMean => 1.0,
                GradeAggregation::Sum => $item->max_points ?? 0.0,
                GradeAggregation::Highest => 0.0,
            };
            $week = $item->due_on === null ? null : $syllabus->teachingWeekOn($item->due_on);

            $rows[] = [
                'item' => $item,
                'share' => !$item->type->carriesPoints() ? 0.0 : ($divisor > 0 ? round($groupShare * $part / $divisor * 100, 1) : null),
                'week' => $week,
                'early_topics' => $week === null
                    ? []
                    : $item->syllabusTopics
                        ->filter(fn (SyllabusTopic $topic): bool => $topic->week !== null && $topic->week > $week)
                        ->pluck('title')
                        ->values()
                        ->all(),
            ];
        }

        return $rows;
    }
}
