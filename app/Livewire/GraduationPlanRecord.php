<?php

namespace App\Livewire;

use App\Actions\Graduation\ManageGraduationPlan;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Services\Graduation\GraduationProgress;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * One graduation plan or stage: its rule, its stages, what a learner must
 * finish, and how far one learner is.
 *
 * Only a published result counts towards a plan. Work still in the gradebook
 * is not a result, so a plan never reads a mark a family has not seen.
 */
class GraduationPlanRecord extends Component
{
    use DispatchesStatusNotifications;
    use ListsSchoolPeople;

    #[Locked]
    public GraduationPlan $plan;

    public bool $isEditing = false;

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    public string $completionOperator = 'all';

    public string $requiredCount = '';

    public bool $usesCredits = false;

    public string $requiredCredits = '';

    public string $stageName = '';

    public string $stageCompletionOperator = 'all';

    public string $stageRequiredCount = '';

    public string $stageRequiredCredits = '';

    public bool $stageIsNegated = false;

    public string $requirementDescription = '';

    public string $requirementSubjectId = '';

    public string $requirementPassMark = '50';

    public string $requirementCredits = '1';

    public bool $requirementIsRequired = true;

    public bool $requirementIsNegated = false;

    #[Url(as: 'student_record_id', except: '')]
    public string $learnerId = '';

    /** @var array<int|string, string> */
    public array $reasons = [];

    public function mount(GraduationPlan $plan): void
    {
        Gate::authorize('view', $plan);

        $this->plan = $plan;
    }

    public function startEditing(): void
    {
        Gate::authorize('update', $this->plan);

        $plan = $this->plan->fresh() ?? $this->plan;
        $this->isEditing = true;
        $this->name = $plan->name;
        $this->description = (string) $plan->description;
        $this->isActive = $plan->is_active;
        $this->completionOperator = $plan->completion_operator;
        $this->requiredCount = (string) $plan->required_count;
        $this->usesCredits = $plan->uses_credits;
        $this->requiredCredits = (string) $plan->required_credits;
    }

    public function stopEditing(): void
    {
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function save(ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('update', $this->plan);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'isActive' => ['boolean'],
            ...$this->ruleRules('completionOperator', 'requiredCount', 'requiredCredits', $this->usesCredits),
        ], $this->ruleMessages('requiredCount', 'requiredCredits'), $this->ruleAttributes('completionOperator', 'requiredCount', 'requiredCredits'));

        try {
            $this->plan = $manageGraduationPlan->update($this->plan, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'is_active' => $this->isActive,
                'completion_operator' => $this->completionOperator,
                'required_count' => $this->requiredCount === '' ? null : (int) $this->requiredCount,
                'uses_credits' => $this->usesCredits,
                'required_credits' => $this->requiredCredits === '' ? null : (int) $this->requiredCredits,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        $this->isEditing = false;
        $this->notify('The plan was saved.');
    }

    public function addStage(ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('update', $this->plan);

        $this->stageName = trim($this->stageName);

        $this->validate([
            'stageName' => ['required', 'string', 'max:100'],
            'stageIsNegated' => ['boolean'],
            ...$this->ruleRules('stageCompletionOperator', 'stageRequiredCount', 'stageRequiredCredits', false),
        ], $this->ruleMessages('stageRequiredCount', 'stageRequiredCredits'), [
            'stageName' => 'stage name',
            ...$this->ruleAttributes('stageCompletionOperator', 'stageRequiredCount', 'stageRequiredCredits'),
        ]);

        try {
            $stage = $manageGraduationPlan->addStage($this->plan, [
                'name' => $this->stageName,
                'description' => null,
                'completion_operator' => $this->stageCompletionOperator,
                'required_count' => $this->stageRequiredCount === '' ? null : (int) $this->stageRequiredCount,
                'required_credits' => $this->stageRequiredCredits === '' ? null : (int) $this->stageRequiredCredits,
                'is_negated' => $this->stageIsNegated,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('stageName', $exception->getMessage());

            return;
        }

        $this->reset('stageName', 'stageCompletionOperator', 'stageRequiredCount', 'stageRequiredCredits', 'stageIsNegated');
        $this->notify("{$stage->name} was added to the plan.");
    }

    public function addRequirement(ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('update', $this->plan);

        $this->requirementDescription = trim($this->requirementDescription);

        $this->validate([
            'requirementDescription' => ['required', 'string', 'max:255'],
            'requirementSubjectId' => ['nullable', 'integer', Rule::exists((new Subject)->getTable(), 'id')->where('school_id', current_school_id())],
            'requirementPassMark' => ['required', 'numeric', 'min:0', 'max:100'],
            'requirementCredits' => ['required', 'integer', 'min:0', 'max:100'],
            'requirementIsRequired' => ['boolean'],
            'requirementIsNegated' => ['boolean'],
        ], [], [
            'requirementDescription' => 'requirement',
            'requirementSubjectId' => 'subject',
            'requirementPassMark' => 'pass mark',
            'requirementCredits' => 'credits',
        ]);

        $manageGraduationPlan->addRequirement($this->plan, [
            'description' => $this->requirementDescription,
            'subject_id' => $this->requirementSubjectId === '' ? null : (int) $this->requirementSubjectId,
            'pass_mark' => (float) $this->requirementPassMark,
            'credits' => (int) $this->requirementCredits,
            'is_required' => $this->requirementIsRequired,
            'is_negated' => $this->requirementIsNegated,
        ], auth()->user());

        $this->reset('requirementDescription', 'requirementSubjectId', 'requirementIsNegated');
        $this->notify('The requirement was added to the plan.');
    }

    public function removeRequirement(int $requirementId, ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('update', $this->plan);

        $manageGraduationPlan->removeRequirement($this->plan->requirements()->findOrFail($requirementId), auth()->user());
        $this->notify('The requirement was removed from the plan.');
    }

    public function excuse(int $requirementId, ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('update', $this->plan);

        $requirement = $this->plan->requirements()->findOrFail($requirementId);
        $learner = $this->learner() ?? abort(404);
        $this->reasons[$requirementId] = trim($this->reasons[$requirementId] ?? '');

        $this->validate(
            ["reasons.{$requirementId}" => ['required', 'string', 'max:500']],
            [],
            ["reasons.{$requirementId}" => 'reason'],
        );

        $manageGraduationPlan->excuse($requirement, $learner, $this->reasons[$requirementId], auth()->user());

        unset($this->reasons[$requirementId]);
        $this->notify('The learner was excused from that requirement.');
    }

    public function takeBack(int $exemptionId, ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('update', $this->plan);

        $exemption = GraduationExemption::query()
            ->whereIn('graduation_requirement_id', $this->plan->requirements()->select('id'))
            ->findOrFail($exemptionId);

        $manageGraduationPlan->takeBack($exemption, auth()->user());
        $this->notify('The excusal was taken back.');
    }

    public function render(GraduationProgress $graduationProgress): View
    {
        $this->plan->refresh()->load(['requirements.subject:id,name', 'cohort:id,name', 'parent:id,name']);
        $this->loadPlanTree();

        $learner = $this->learner();
        $activeStages = $this->plan->children->where('is_active', true)->count();

        return view('livewire.graduation-plan-record', [
            'canWrite' => Gate::allows('update', $this->plan),
            'subjects' => Subject::query()->inSchool()->orderBy('name')->get(['id', 'name']),
            'students' => $this->schoolLearners(),
            'learner' => $learner,
            'progress' => $learner === null ? null : $graduationProgress->for($this->plan, $learner),
            'exemptions' => $learner === null ? collect() : GraduationExemption::query()
                ->where('student_record_id', $learner->id)
                ->whereIn('graduation_requirement_id', $this->plan->requirements->pluck('id'))
                ->get()
                ->keyBy('graduation_requirement_id'),
            'countedItems' => $this->plan->requirements->where('is_required', true)->count() + $activeStages,
            'isEmpty' => $this->plan->requirements->isEmpty() && $activeStages === 0,
        ]);
    }

    private function learner(): ?StudentRecord
    {
        return ctype_digit($this->learnerId)
            ? StudentRecord::query()->inSchool()->with('user:id,name')->find((int) $this->learnerId)
            : null;
    }

    /**
     * Load all nested stages without querying once per branch in the view.
     */
    private function loadPlanTree(): void
    {
        $plansByParent = GraduationPlan::query()
            ->inSchool()
            ->whereNotNull('parent_id')
            ->orderBy('position')
            ->orderBy('id')
            ->with('requirements')
            ->get()
            ->groupBy('parent_id');

        $attachChildren = function (GraduationPlan $stage) use (&$attachChildren, $plansByParent): void {
            $children = $plansByParent->get($stage->id, collect())->values();
            $stage->setRelation('children', $children);

            foreach ($children as $child) {
                $attachChildren($child);
            }
        };

        $attachChildren($this->plan);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function ruleRules(string $operator, string $count, string $credits, bool $countsCredits): array
    {
        return [
            $operator => ['required', Rule::in(ManageGraduationPlan::OPERATORS)],
            $count => [Rule::requiredIf($this->{$operator} === 'at_least'), 'nullable', 'integer', 'min:1', 'max:1000'],
            $credits => [Rule::requiredIf($countsCredits || $this->{$operator} === 'at_least_credits'), 'nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function ruleMessages(string $count, string $credits): array
    {
        return [
            "{$credits}.required" => 'A plan that counts credits must say how many are needed.',
            "{$count}.required" => 'Say how many items are needed.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function ruleAttributes(string $operator, string $count, string $credits): array
    {
        return [$operator => 'rule', $count => 'number needed', $credits => 'credits needed'];
    }
}
