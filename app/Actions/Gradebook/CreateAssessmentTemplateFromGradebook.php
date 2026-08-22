<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CreateAssessmentTemplateFromGradebook
{
    public function __construct(private RecordAuditEvent $audit)
    {
    }

    /**
     * Capture a configured gradebook as a reusable structure for the school.
     *
     * Due dates and learner marks intentionally stay on the course offering.
     */
    public function create(CourseOffering $courseOffering, string $name, ?string $description, User $actor): AssessmentTemplate
    {
        $courseOffering->loadMissing(['gradeCategories', 'gradeItems']);

        if ($courseOffering->gradeCategories->isEmpty() && $courseOffering->gradeItems->isEmpty()) {
            throw new InvalidValueException('Add at least one category or assessment before saving this gradebook as a template.');
        }

        return DB::transaction(function () use ($courseOffering, $name, $description, $actor): AssessmentTemplate {
            $template = AssessmentTemplate::create([
                'school_id'   => $courseOffering->school_id,
                'name'        => $name,
                'description' => $description,
                'created_by'  => $actor->id,
            ]);
            $categoryIds = $this->copyCategories($courseOffering->gradeCategories, $template);

            foreach ($courseOffering->gradeItems->sortBy([['position', 'asc'], ['id', 'asc']]) as $item) {
                $template->items()->create([
                    'assessment_template_category_id' => $item->grade_category_id === null ? null : $categoryIds[$item->grade_category_id],
                    'name'                            => $item->name,
                    'type'                            => $item->type,
                    'grading_scale_id'                => $item->grading_scale_id,
                    'max_points'                      => $item->max_points,
                    'weight'                          => $item->weight,
                    'position'                        => $item->position,
                ]);
            }

            $this->audit->record(AuditAction::AssessmentTemplateSaved, $template, ['course_offering_id' => $courseOffering->id], $actor);

            return $template;
        });
    }

    /**
     * @param Collection<int, GradeCategory> $categories
     *
     * @return array<int, int>
     */
    private function copyCategories(Collection $categories, AssessmentTemplate $template): array
    {
        $templateCategoryIds = [];
        $create = function (GradeCategory $category) use (&$create, &$templateCategoryIds, $categories, $template): void {
            if (isset($templateCategoryIds[$category->id])) {
                return;
            }

            if ($category->parent_id !== null) {
                $parent = $categories->firstWhere('id', $category->parent_id);

                if ($parent instanceof GradeCategory) {
                    $create($parent);
                }
            }

            $templateCategory = $template->categories()->create([
                'parent_id'   => $category->parent_id === null ? null : $templateCategoryIds[$category->parent_id],
                'name'        => $category->name,
                'aggregation' => $category->aggregation,
                'weight'      => $category->weight,
                'position'    => $category->position,
            ]);
            $templateCategoryIds[$category->id] = $templateCategory->id;
        };

        foreach ($categories as $category) {
            $create($category);
        }

        return $templateCategoryIds;
    }
}
