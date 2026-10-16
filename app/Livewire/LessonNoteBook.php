<?php

namespace App\Livewire;

use App\Enums\LessonNoteStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\LessonNote;
use App\Models\Syllabus;
use App\Services\Syllabus\LessonNoteService;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Write weekly lesson notes for one class, and review them as head of department.
 */
class LessonNoteBook extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    /** The chosen track: a section id, or an empty string for the whole class. */
    #[Url(as: 'class')]
    public string $track = '';

    /** Show only notes in this status, or every note when empty. */
    #[Url]
    public string $status = '';

    public ?int $editingId = null;

    public ?int $week = null;

    public string $topicId = '';

    public string $objectives = '';

    public string $activities = '';

    public string $evaluation = '';

    /** @var array<int|string, string> Review notes typed per lesson note id. */
    public array $reviewNotes = [];

    public function mount(SyllabusCoverageService $coverage): void
    {
        Gate::authorize('viewAny', [LessonNote::class, $this->syllabus]);

        $trackIds = array_map(fn (array $option): string => (string) $option['id'], $coverage->tracks($this->syllabus));

        if (!in_array($this->track, $trackIds, true)) {
            $this->track = $trackIds[0];
        }

        $this->week = $this->syllabus->teachingWeekOn() ?? 1;
    }

    public function updatedTrack(): void
    {
        $this->cancel();
    }

    public function edit(int $noteId): void
    {
        $note = $this->findNote($noteId);
        Gate::authorize('update', $note);

        $this->editingId = $note->id;
        $this->week = $note->week;
        $this->topicId = (string) ($note->syllabus_topic_id ?? '');
        $this->objectives = $note->objectives;
        $this->activities = $note->activities;
        $this->evaluation = (string) $note->evaluation;
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->reset('editingId', 'topicId', 'objectives', 'activities', 'evaluation');
        $this->week = $this->syllabus->teachingWeekOn() ?? 1;
        $this->resetValidation();
    }

    public function save(LessonNoteService $lessonNotes): void
    {
        $note = $this->editingId === null ? null : $this->findNote($this->editingId);

        if ($note === null) {
            Gate::authorize('create', [LessonNote::class, $this->syllabus, $this->trackId()]);
        } else {
            Gate::authorize('update', $note);
        }

        $validated = $this->validate([
            'week' => ['required', 'integer', 'min:1', 'max:60'],
            'topicId' => ['nullable', Rule::exists('syllabus_topics', 'id')->where('syllabus_id', $this->syllabus->id)],
            'objectives' => ['required', 'string', 'max:5000'],
            'activities' => ['required', 'string', 'max:10000'],
            'evaluation' => ['nullable', 'string', 'max:5000'],
        ], [
            'objectives.required' => 'Say what the students should be able to do after the lessons.',
            'activities.required' => 'Say how the lessons will be taught.',
        ]);

        try {
            $lessonNotes->save($this->syllabus, $this->trackId(), [
                'week' => (int) $validated['week'],
                'syllabus_topic_id' => $this->topicId === '' ? null : (int) $this->topicId,
                'objectives' => $this->objectives,
                'activities' => $this->activities,
                'evaluation' => $this->evaluation === '' ? null : $this->evaluation,
            ], auth()->user(), $note);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify($note === null ? "Saved the note for week {$validated['week']}." : "Updated the note for week {$validated['week']}.");
        $this->cancel();
    }

    public function submit(int $noteId, LessonNoteService $lessonNotes): void
    {
        $note = $this->findNote($noteId);
        Gate::authorize('submit', $note);

        $this->run(fn () => $lessonNotes->submit($note, auth()->user()), "Sent the note for week {$note->week} for review.");
    }

    public function delete(int $noteId, LessonNoteService $lessonNotes): void
    {
        $note = $this->findNote($noteId);
        Gate::authorize('delete', $note);

        if ($this->editingId === $note->id) {
            $this->cancel();
        }

        $this->run(fn () => $lessonNotes->delete($note), "Deleted the note for week {$note->week}.");
    }

    public function approve(int $noteId, LessonNoteService $lessonNotes): void
    {
        $note = $this->findNote($noteId);
        Gate::authorize('review', $note);

        $this->run(fn () => $lessonNotes->approve($note, auth()->user()), "Approved the note for week {$note->week}.");
    }

    public function sendBack(int $noteId, LessonNoteService $lessonNotes): void
    {
        $note = $this->findNote($noteId);
        Gate::authorize('review', $note);
        $this->validate(
            ["reviewNotes.{$noteId}" => ['required', 'string', 'max:2000']],
            ["reviewNotes.{$noteId}.required" => 'Say what needs to change.'],
        );

        if ($this->run(fn () => $lessonNotes->sendBack($note, $this->reviewNotes[$noteId], auth()->user()), "Sent the note for week {$note->week} back to its author.")) {
            unset($this->reviewNotes[$noteId]);
        }
    }

    public function render(SyllabusCoverageService $coverage): View
    {
        $notes = LessonNote::query()
            ->where('course_offering_id', $this->syllabus->course_offering_id)
            ->where('academic_cycle_section_id', $this->trackId())
            ->when(LessonNoteStatus::tryFrom($this->status), fn ($query, LessonNoteStatus $status) => $query->where('status', $status))
            ->with(['topic:id,title', 'author:id,name', 'reviewedBy:id,name', 'courseOffering:id,school_id'])
            ->orderByDesc('week')
            ->latest('id')
            ->get();

        return view('livewire.lesson-note-book', [
            'tracks' => $coverage->tracks($this->syllabus),
            'topics' => $this->syllabus->topics()->get(['syllabus_topics.id', 'week', 'title']),
            'notes' => $notes,
            'canWrite' => Gate::allows('create', [LessonNote::class, $this->syllabus, $this->trackId()]),
            'statuses' => LessonNoteStatus::cases(),
        ]);
    }

    /**
     * Find a note of this offering and the chosen class.
     */
    private function findNote(int $noteId): LessonNote
    {
        return LessonNote::query()
            ->where('course_offering_id', $this->syllabus->course_offering_id)
            ->where('academic_cycle_section_id', $this->trackId())
            ->findOrFail($noteId);
    }

    /**
     * Run a change and report how it went.
     */
    private function run(callable $change, string $message): bool
    {
        try {
            $change();
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return false;
        }

        $this->notify($message);

        return true;
    }

    private function trackId(): ?int
    {
        return $this->track === '' ? null : (int) $this->track;
    }
}
