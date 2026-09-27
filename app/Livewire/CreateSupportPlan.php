<?php

namespace App\Livewire;

use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\SupportCategory;
use App\Exceptions\InvalidValueException;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Open a support plan for one enrolled child.
 */
class CreateSupportPlan extends Component
{
    use ListsSchoolPeople;

    public ?int $studentRecordId = null;

    public string $category = '';

    public string $title = '';

    public string $summary = '';

    public string $startsOn = '';

    public string $reviewOn = '';

    public ?int $assignedTo = null;

    public function mount(): void
    {
        Gate::authorize('create', SupportPlan::class);

        $this->category = SupportCategory::Intervention->value;
        $this->startsOn = now()->toDateString();
    }

    public function save(ManageSupportPlan $manageSupportPlan): void
    {
        Gate::authorize('create', SupportPlan::class);

        $this->validate([
            'studentRecordId' => ['required', 'integer', Rule::exists('student_records', 'id')->where('school_id', current_school_id())],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(SupportCategory::class)],
            'summary' => ['nullable', 'string', 'max:5000'],
            'startsOn' => ['nullable', 'date'],
            'reviewOn' => ['nullable', 'date', 'after_or_equal:startsOn'],
            'assignedTo' => ['nullable', 'integer', Rule::exists('school_memberships', 'user_id')->where('school_id', current_school_id())],
        ], [
            'studentRecordId.required' => 'Choose the learner.',
            'reviewOn.after_or_equal' => 'A plan cannot be reviewed before it starts.',
        ]);

        try {
            $plan = $manageSupportPlan->open(
                enrollment: StudentRecord::query()->inSchool()->findOrFail($this->studentRecordId),
                title: $this->title,
                category: SupportCategory::from($this->category),
                summary: $this->summary === '' ? null : $this->summary,
                startsOn: $this->startsOn === '' ? null : $this->startsOn,
                reviewOn: $this->reviewOn === '' ? null : $this->reviewOn,
                owner: $this->assignedTo === null ? null : User::findOrFail($this->assignedTo),
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('studentRecordId', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The plan was opened.');
        $this->redirectRoute('support-plans.show', $plan);
    }

    public function render(): View
    {
        return view('livewire.create-support-plan', [
            'categories' => SupportCategory::cases(),
            'isConfidential' => SupportCategory::tryFrom($this->category)?->isConfidential() ?? false,
            'students' => $this->schoolLearners(),
            'staff' => $this->schoolStaff(),
        ]);
    }
}
