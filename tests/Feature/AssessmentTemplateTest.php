<?php

namespace Tests\Feature;

use App\Actions\Gradebook\ApplyAssessmentTemplate;
use App\Actions\Gradebook\CreateAssessmentTemplateFromGradebook;
use App\Enums\GradeItemType;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\Subject;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTemplateTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_gradebook_structure_can_be_saved_and_applied_to_an_empty_offering(): void
    {
        $actor = $this->memberOf($this->workingSchool());
        $source = $this->courseOffering();
        $category = GradeCategory::create([
            'school_id' => $source->school_id,
            'course_offering_id' => $source->id,
            'name' => 'Continuous assessment',
            'weight' => 2,
        ]);
        GradeItem::create([
            'school_id' => $source->school_id,
            'course_offering_id' => $source->id,
            'grade_category_id' => $category->id,
            'name' => 'Classwork',
            'type' => GradeItemType::Numeric,
            'max_points' => 20,
        ]);

        $template = app(CreateAssessmentTemplateFromGradebook::class)->create($source, 'Term assessment', 'A common structure.', $actor);
        $target = $this->courseOffering();

        app(ApplyAssessmentTemplate::class)->apply($template, $target, $actor);

        $this->assertSame('Term assessment', $template->name);
        $this->assertCount(1, $template->categories);
        $this->assertCount(1, $template->items);
        $this->assertSame('Classwork', $target->gradeItems()->sole()->name);
        $this->assertSame('Continuous assessment', $target->gradeCategories()->sole()->name);
        $this->assertSame($template->id, $template->applications()->sole()->assessment_template_id);
    }

    public function test_a_template_cannot_be_applied_over_an_existing_gradebook(): void
    {
        $actor = $this->memberOf($this->workingSchool());
        $source = $this->courseOffering();
        GradeItem::create([
            'school_id' => $source->school_id,
            'course_offering_id' => $source->id,
            'name' => 'Baseline assessment',
            'max_points' => 20,
        ]);
        $template = app(CreateAssessmentTemplateFromGradebook::class)->create($source, 'Term assessment', null, $actor);
        $target = $this->courseOffering();
        GradeItem::create([
            'school_id' => $target->school_id,
            'course_offering_id' => $target->id,
            'name' => 'Existing assessment',
            'max_points' => 20,
        ]);

        $this->expectException(InvalidValueException::class);

        app(ApplyAssessmentTemplate::class)->apply($template, $target, $actor);
    }

    private function courseOffering(): CourseOffering
    {
        $school = $this->workingSchool();
        $source = CourseOffering::factory()->create(['school_id' => $school->id]);
        $subject = Subject::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);

        return CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $source->academic_year_id,
            'academic_period_id' => $source->academic_period_id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ]);
    }
}
