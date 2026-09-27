<?php

namespace App\Livewire;

use App\Enums\SyllabusStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\GradeItem;
use App\Models\Syllabus;
use App\Services\Syllabus\AssessmentPlanService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show how the offering is assessed, and link each assessment to the topics it tests.
 */
class SyllabusAssessmentPlan extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    public ?int $editingItemId = null;

    /** @var list<int|string> */
    public array $topicIds = [];

    public function mount(): void
    {
        Gate::authorize('view', $this->syllabus);
    }

    public function edit(int $itemId): void
    {
        Gate::authorize('manageGradebook', $this->syllabus->courseOffering);
        $item = $this->findItem($itemId);

        $this->editingItemId = $item->id;
        $this->topicIds = $item->syllabusTopics()->where('syllabus_id', $this->syllabus->id)->pluck('syllabus_topics.id')->map(fn (int $id): string => (string) $id)->all();
    }

    public function cancel(): void
    {
        $this->reset('editingItemId', 'topicIds');
    }

    public function saveTopics(AssessmentPlanService $plan): void
    {
        Gate::authorize('manageGradebook', $this->syllabus->courseOffering);

        if ($this->editingItemId === null) {
            return;
        }

        $item = $this->findItem($this->editingItemId);

        try {
            $plan->tagTopics($this->syllabus, $item, array_map('intval', $this->topicIds));
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("Saved the topics that {$item->name} tests.");
        $this->cancel();
    }

    public function render(AssessmentPlanService $plan): View
    {
        $isPublished = $this->syllabus->status === SyllabusStatus::Published;

        return view('livewire.syllabus-assessment-plan', [
            'plan' => $plan->plan($this->syllabus),
            'untested' => $plan->untestedTopics($this->syllabus),
            'topics' => $this->syllabus->topics()->get(['syllabus_topics.id', 'week', 'title']),
            'canTag' => $isPublished && Gate::allows('manageGradebook', $this->syllabus->courseOffering),
            'canOpenGradebook' => Gate::allows('viewGradebook', $this->syllabus->courseOffering),
        ]);
    }

    private function findItem(int $itemId): GradeItem
    {
        return GradeItem::query()->forCourseOffering($this->syllabus->course_offering_id)->findOrFail($itemId);
    }
}
