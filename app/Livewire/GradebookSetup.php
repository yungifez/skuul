<?php

namespace App\Livewire;

use App\Actions\Gradebook\ApplyAssessmentTemplate;
use App\Actions\Gradebook\CreateAssessmentTemplateFromGradebook;
use App\Enums\GradeAggregation;
use App\Enums\GradeItemType;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\GradingScale;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Set up the work a course is marked on: categories, assessments, templates.
 *
 * Structure changes only while the period takes new work. A change never
 * touches a mark, so the maximum of an assessment cannot drop below a mark
 * a learner already has.
 */
class GradebookSetup extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public CourseOffering $courseOffering;

    public bool $isAddingItem = false;

    #[Locked]
    public ?int $editingItemId = null;

    public string $itemName = '';

    public string $itemType = 'numeric';

    public string $itemMaxPoints = '';

    public string $itemScaleId = '';

    public string $itemWeight = '1';

    public string $itemCategoryId = '';

    public string $itemDueOn = '';

    public bool $isAddingCategory = false;

    public string $categoryName = '';

    public string $categoryAggregation = '';

    public string $categoryWeight = '1';

    public string $templateId = '';

    public bool $isSavingTemplate = false;

    public string $templateName = '';

    public string $templateDescription = '';

    public function mount(CourseOffering $courseOffering): void
    {
        Gate::authorize('manageGradebook', $courseOffering);

        $this->courseOffering = $courseOffering;
        $this->categoryAggregation = GradeAggregation::WeightedMean->value;
        $this->isAddingItem = !$courseOffering->gradeItems()->exists();
    }

    public function startAddingItem(): void
    {
        $this->resetItemForm();
        $this->isAddingItem = true;
    }

    public function editItem(int $itemId): void
    {
        $item = $this->item($itemId);

        $this->resetItemForm();
        $this->editingItemId = $item->id;
        $this->itemName = $item->name;
        $this->itemType = $item->type->value;
        $this->itemMaxPoints = $item->max_points === null ? '' : (string) $item->max_points;
        $this->itemScaleId = (string) $item->grading_scale_id;
        $this->itemWeight = (string) $item->weight;
        $this->itemCategoryId = (string) $item->grade_category_id;
        $this->itemDueOn = (string) $item->due_on?->format('Y-m-d');
    }

    public function cancelItem(): void
    {
        $this->resetItemForm();
    }

    public function saveItem(): void
    {
        $this->authorizeStructureChange();

        $item = $this->editingItemId === null ? null : $this->item($this->editingItemId);
        $type = $item === null ? GradeItemType::tryFrom($this->itemType) : $item->type;

        $this->validate([
            'itemName' => ['required', 'string', 'max:150'],
            'itemType' => [Rule::requiredIf($item === null), Rule::enum(GradeItemType::class)],
            'itemMaxPoints' => [Rule::requiredIf($type === GradeItemType::Numeric), 'nullable', 'numeric', 'gt:0', 'max:999999.99'],
            'itemScaleId' => [Rule::requiredIf($item === null && $type === GradeItemType::Scale), 'nullable', 'integer', Rule::exists((new GradingScale)->getTable(), 'id')->where(fn (Builder $query) => $query->where('school_id', current_school_id())->where('is_active', true))],
            'itemWeight' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
            'itemCategoryId' => ['nullable', 'integer', Rule::exists('grade_categories', 'id')->where('course_offering_id', $this->courseOffering->id)],
            'itemDueOn' => ['nullable', 'date_format:Y-m-d', 'after:2000-01-01', 'before:2100-01-01'],
        ], [
            'itemMaxPoints.required' => 'Say what the work is marked out of.',
            'itemScaleId.required' => 'Choose the grading scale.',
            'itemDueOn.after' => 'Check the year of the due date.',
            'itemDueOn.before' => 'Check the year of the due date.',
            'itemDueOn.date_format' => 'Check the due date.',
        ], [
            'itemName' => 'name',
            'itemMaxPoints' => 'maximum',
            'itemWeight' => 'weight',
            'itemCategoryId' => 'category',
            'itemDueOn' => 'due date',
        ]);

        $attributes = [
            'name' => trim($this->itemName),
            'weight' => (float) $this->itemWeight,
            'grade_category_id' => $this->itemCategoryId === '' ? null : (int) $this->itemCategoryId,
            'due_on' => $this->itemDueOn === '' ? null : $this->itemDueOn,
        ];

        if ($type === GradeItemType::Numeric) {
            $attributes['max_points'] = (float) $this->itemMaxPoints;
            $highestMark = $item?->entries()->max('points');

            if ($highestMark !== null && (float) $highestMark > $attributes['max_points']) {
                $this->addError('itemMaxPoints', 'A learner already has '.(float) $highestMark.'. The maximum cannot go below that.');

                return;
            }
        }

        if ($item !== null) {
            $item->update($attributes);
            $this->resetItemForm();
            $this->notify('Assessment changed.');
            $this->dispatch('gradebook-changed');

            return;
        }

        $scale = $type === GradeItemType::Scale ? GradingScale::query()->inSchool()->findOrFail((int) $this->itemScaleId) : null;
        $scaleMaximum = $scale?->options()->max('points');

        GradeItem::create($attributes + [
            'school_id' => $this->courseOffering->school_id,
            'course_offering_id' => $this->courseOffering->id,
            'type' => $type,
            'grading_scale_id' => $scale?->id,
            'max_points' => match ($type) {
                GradeItemType::Numeric => $attributes['max_points'],
                GradeItemType::Scale => $scaleMaximum === null ? null : (float) $scaleMaximum,
                default => null,
            },
            'created_by' => auth()->id(),
        ]);

        $this->resetItemForm();
        $this->notify('Assessment added.');
        $this->dispatch('gradebook-changed');
    }

    public function removeItem(int $itemId): void
    {
        $this->authorizeStructureChange();
        $item = $this->item($itemId);

        if ($item->entries()->exists()) {
            $this->notify("{$item->name} has marks. Remove them first.", 'danger');

            return;
        }

        $item->delete();

        if ($this->editingItemId === $itemId) {
            $this->resetItemForm();
        }

        $this->notify('Assessment removed.');
        $this->dispatch('gradebook-changed');
    }

    public function addCategory(): void
    {
        $this->authorizeStructureChange();

        $this->validate([
            'categoryName' => ['required', 'string', 'max:100', Rule::unique('grade_categories', 'name')->where('course_offering_id', $this->courseOffering->id)],
            'categoryAggregation' => ['required', Rule::enum(GradeAggregation::class)],
            'categoryWeight' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
        ], [
            'categoryName.unique' => 'This course already has a category with this name.',
        ], [
            'categoryName' => 'name',
            'categoryAggregation' => 'calculation',
            'categoryWeight' => 'weight',
        ]);

        GradeCategory::create([
            'school_id' => $this->courseOffering->school_id,
            'course_offering_id' => $this->courseOffering->id,
            'name' => trim($this->categoryName),
            'aggregation' => $this->categoryAggregation,
            'weight' => (float) $this->categoryWeight,
            'position' => (int) $this->courseOffering->gradeCategories()->max('position') + 1,
        ]);

        $this->reset('isAddingCategory', 'categoryName', 'categoryWeight');
        $this->notify('Category added.');
    }

    public function applyTemplate(ApplyAssessmentTemplate $applyAssessmentTemplate): void
    {
        $this->authorizeStructureChange();

        $this->validate([
            'templateId' => ['required', 'integer', Rule::exists((new AssessmentTemplate)->getTable(), 'id')->where(fn (Builder $query) => $query->where('school_id', current_school_id())->where('is_active', true))],
        ], ['templateId.required' => 'Choose a template.'], ['templateId' => 'template']);

        try {
            $applyAssessmentTemplate->apply(AssessmentTemplate::query()->inSchool()->findOrFail((int) $this->templateId), $this->courseOffering, auth()->user());
        } catch (ClosedPeriodException|InvalidValueException $exception) {
            $this->addError('templateId', $exception->getMessage());

            return;
        }

        $this->reset('templateId', 'isAddingItem');
        $this->notify('Template applied.');
        $this->dispatch('gradebook-changed');
    }

    public function saveTemplate(CreateAssessmentTemplateFromGradebook $createTemplate): void
    {
        Gate::authorize('manageGradebook', $this->courseOffering);

        $this->validate([
            'templateName' => ['required', 'string', 'max:150', Rule::unique('assessment_templates', 'name')->where('school_id', current_school_id())],
            'templateDescription' => ['nullable', 'string', 'max:5000'],
        ], [
            'templateName.unique' => 'Your school already has a template with this name.',
        ], [
            'templateName' => 'name',
            'templateDescription' => 'description',
        ]);

        try {
            $createTemplate->create($this->courseOffering, trim($this->templateName), trim($this->templateDescription) === '' ? null : trim($this->templateDescription), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('templateName', $exception->getMessage());

            return;
        }

        $this->reset('isSavingTemplate', 'templateName', 'templateDescription');
        $this->notify('Saved as a template for your school.');
    }

    public function render(): View
    {
        $items = $this->courseOffering->gradeItems()
            ->with(['category:id,name', 'gradingScale:id,name'])
            ->withCount('entries')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
        $categories = $this->courseOffering->gradeCategories()->orderBy('position')->orderBy('id')->get();

        return view('livewire.gradebook-setup', [
            'items' => $items,
            'categories' => $categories,
            'types' => GradeItemType::cases(),
            'aggregations' => GradeAggregation::cases(),
            'scales' => GradingScale::query()->inSchool()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'scale_type']),
            'templates' => $items->isEmpty() && $categories->isEmpty()
                ? AssessmentTemplate::query()->inSchool()->where('is_active', true)->withCount(['categories', 'items'])->orderBy('name')->get()
                : collect(),
        ]);
    }

    /**
     * Check the person may change the structure, and that the period takes it.
     */
    private function authorizeStructureChange(): void
    {
        Gate::authorize('manageGradebook', $this->courseOffering);

        $this->courseOffering->loadMissing(['academicPeriod', 'academicYear']);
        $period = $this->courseOffering->academicPeriod ?? $this->courseOffering->academicYear;

        abort_if($period !== null && !$period->status->acceptsNewWork(), 403, 'The period is closing or closed, so the assessments cannot change.');
    }

    private function item(int $itemId): GradeItem
    {
        return $this->courseOffering->gradeItems()->findOrFail($itemId);
    }

    private function resetItemForm(): void
    {
        $this->reset('isAddingItem', 'editingItemId', 'itemName', 'itemType', 'itemMaxPoints', 'itemScaleId', 'itemWeight', 'itemCategoryId', 'itemDueOn');
        $this->resetValidation();
    }
}
