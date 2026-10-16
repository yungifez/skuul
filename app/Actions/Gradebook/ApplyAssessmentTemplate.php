<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\GradeItemType;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateCategory;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApplyAssessmentTemplate
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Copy an approved school template into a blank course-offering gradebook.
     *
     * @throws ClosedPeriodException
     * @throws InvalidValueException
     */
    public function apply(AssessmentTemplate $template, CourseOffering $courseOffering, User $actor): void
    {
        $this->ensureCanApply($template, $courseOffering);
        $template->loadMissing(['categories', 'items.gradingScale']);

        DB::transaction(function () use ($template, $courseOffering, $actor): void {
            $categoryIds = $this->copyCategories($template->categories, $courseOffering);

            foreach ($template->items as $item) {
                GradeItem::create([
                    'school_id' => $courseOffering->school_id,
                    'course_offering_id' => $courseOffering->id,
                    'grade_category_id' => $item->assessment_template_category_id === null ? null : $categoryIds[$item->assessment_template_category_id],
                    'name' => $item->name,
                    'type' => $item->type,
                    'grading_scale_id' => $item->grading_scale_id,
                    'max_points' => $item->max_points,
                    'weight' => $item->weight,
                    'position' => $item->position,
                    'created_by' => $actor->id,
                ]);
            }

            $template->applications()->create([
                'course_offering_id' => $courseOffering->id,
                'applied_by' => $actor->id,
                'applied_at' => now(),
            ]);
            $this->audit->record(AuditAction::AssessmentTemplateApplied, $template, ['course_offering_id' => $courseOffering->id], $actor);
        });
    }

    /**
     * @throws ClosedPeriodException
     * @throws InvalidValueException
     */
    private function ensureCanApply(AssessmentTemplate $template, CourseOffering $courseOffering): void
    {
        if ($template->school_id !== $courseOffering->school_id || !$template->is_active) {
            throw new InvalidValueException('Choose an active assessment template from this school.');
        }

        $template->loadMissing('items.gradingScale');

        if ($template->items->contains(fn ($item): bool => $item->type === GradeItemType::Scale && $item->gradingScale === null)) {
            throw new InvalidValueException('This template uses a grading scale that is no longer available. Choose another template or update the scale first.');
        }

        $courseOffering->loadMissing(['academicPeriod', 'academicYear']);
        $period = $courseOffering->academicPeriod ?? $courseOffering->academicYear;

        if ($period !== null && $period->isClosed()) {
            throw new ClosedPeriodException('You cannot change a gradebook in a closed academic period.');
        }

        if ($courseOffering->gradeCategories()->exists() || $courseOffering->gradeItems()->exists()) {
            throw new InvalidValueException('This gradebook already has a structure. Apply templates before adding categories or assessments.');
        }
    }

    /**
     * @param  Collection<int, AssessmentTemplateCategory>  $categories
     * @return array<int, int>
     */
    private function copyCategories(Collection $categories, CourseOffering $courseOffering): array
    {
        $gradeCategoryIds = [];
        $create = function (AssessmentTemplateCategory $category) use (&$create, &$gradeCategoryIds, $categories, $courseOffering): void {
            if (isset($gradeCategoryIds[$category->id])) {
                return;
            }

            if ($category->parent_id !== null) {
                $parent = $categories->firstWhere('id', $category->parent_id);

                if ($parent instanceof AssessmentTemplateCategory) {
                    $create($parent);
                }
            }

            $gradeCategory = GradeCategory::create([
                'school_id' => $courseOffering->school_id,
                'course_offering_id' => $courseOffering->id,
                'parent_id' => $category->parent_id === null ? null : $gradeCategoryIds[$category->parent_id],
                'name' => $category->name,
                'aggregation' => $category->aggregation,
                'weight' => $category->weight,
                'position' => $category->position,
            ]);
            $gradeCategoryIds[$category->id] = $gradeCategory->id;
        };

        foreach ($categories as $category) {
            $create($category);
        }

        return $gradeCategoryIds;
    }
}
