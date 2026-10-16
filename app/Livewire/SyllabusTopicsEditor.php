<?php

namespace App\Livewire;

use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Plan the weekly topics of a draft syllabus.
 */
class SyllabusTopicsEditor extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    public ?int $editingTopicId = null;

    public ?int $week = null;

    public string $title = '';

    public string $objectives = '';

    public string $content = '';

    public string $resources = '';

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'week' => ['nullable', 'integer', 'min:1', 'max:60'],
            'title' => ['required', 'string', 'max:255'],
            'objectives' => ['nullable', 'string', 'max:5000'],
            'content' => ['nullable', 'string', 'max:10000'],
            'resources' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function saveTopic(SyllabusService $syllabusService): void
    {
        Gate::authorize('update', $this->syllabus);
        $validated = $this->validate();

        try {
            $syllabusService->saveTopic($this->syllabus, [
                'title' => $validated['title'],
                'week' => $validated['week'],
                'objectives' => $this->nullIfBlank($validated['objectives']),
                'content' => $this->nullIfBlank($validated['content']),
                'resources' => $this->nullIfBlank($validated['resources']),
            ], $this->editingTopic());
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify($this->editingTopicId === null ? "Added {$validated['title']}." : "Saved {$validated['title']}.");
        $this->resetForm();
    }

    public function editTopic(int $topicId): void
    {
        Gate::authorize('update', $this->syllabus);
        $topic = $this->syllabus->topics()->findOrFail($topicId);

        $this->resetValidation();
        $this->editingTopicId = $topic->id;
        $this->week = $topic->week;
        $this->title = $topic->title;
        $this->objectives = (string) $topic->objectives;
        $this->content = (string) $topic->content;
        $this->resources = (string) $topic->resources;
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function deleteTopic(int $topicId, SyllabusService $syllabusService): void
    {
        Gate::authorize('update', $this->syllabus);
        $topic = $this->syllabus->topics()->findOrFail($topicId);

        try {
            $syllabusService->deleteTopic($this->syllabus, $topic);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        if ($this->editingTopicId === $topicId) {
            $this->resetForm();
        }

        $this->notify("Removed {$topic->title}.");
    }

    public function render(): View
    {
        return view('livewire.syllabus-topics-editor', [
            'topicsByWeek' => $this->syllabus->topics()->get()->groupBy(fn (SyllabusTopic $topic): string => $topic->week === null ? 'Unscheduled' : "Week {$topic->week}"),
        ]);
    }

    private function editingTopic(): ?SyllabusTopic
    {
        return $this->editingTopicId === null ? null : $this->syllabus->topics()->findOrFail($this->editingTopicId);
    }

    private function resetForm(): void
    {
        $this->reset(['editingTopicId', 'week', 'title', 'objectives', 'content', 'resources']);
        $this->resetValidation();
    }

    private function nullIfBlank(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
