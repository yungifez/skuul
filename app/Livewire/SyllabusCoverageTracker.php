<?php

namespace App\Livewire;

use App\Enums\TopicCoverageStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Record, class by class, which planned topics were actually taught.
 */
class SyllabusCoverageTracker extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    /** The chosen track: a section id, or an empty string for the whole class. */
    #[Url(as: 'class')]
    public string $track = '';

    public function mount(SyllabusCoverageService $coverage): void
    {
        Gate::authorize('view', $this->syllabus);

        $trackIds = array_map(fn (array $option): string => (string) $option['id'], $coverage->tracks($this->syllabus));

        if (!in_array($this->track, $trackIds, true)) {
            $this->track = $trackIds[0];
        }
    }

    public function mark(int $topicId, string $status, SyllabusCoverageService $coverage): void
    {
        Gate::authorize('recordCoverage', $this->syllabus);
        $topic = $this->syllabus->topics()->findOrFail($topicId);
        $existing = $coverage->coverageFor($this->syllabus, $this->trackId())->get($topicId);

        try {
            $coverage->record($this->syllabus, $topic, $this->trackId(), TopicCoverageStatus::tryFrom($status), note: $existing?->note);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify($status === '' ? "Cleared {$topic->title}." : "Marked {$topic->title} as ".strtolower(TopicCoverageStatus::from($status)->label()).'.');
    }

    public function saveNote(int $topicId, string $note, SyllabusCoverageService $coverage): void
    {
        Gate::authorize('recordCoverage', $this->syllabus);
        $topic = $this->syllabus->topics()->findOrFail($topicId);
        $existing = $coverage->coverageFor($this->syllabus, $this->trackId())->get($topicId);
        $note = trim(mb_substr($note, 0, 1000));

        if ($existing === null) {
            $this->notify('Mark how far the topic was taught before adding a note.', 'danger');

            return;
        }

        try {
            $coverage->record($this->syllabus, $topic, $this->trackId(), $existing->status, $existing->covered_on?->toDateString(), $note === '' ? null : $note);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("Saved the note on {$topic->title}.");
    }

    public function render(SyllabusCoverageService $coverage): View
    {
        return view('livewire.syllabus-coverage-tracker', [
            'tracks' => $coverage->tracks($this->syllabus),
            'topics' => $this->syllabus->topics()->get(),
            'coverages' => $coverage->coverageFor($this->syllabus, $this->trackId()),
            'summary' => collect($coverage->summary($this->syllabus))->firstWhere('id', $this->trackId()),
            'canRecord' => Gate::allows('recordCoverage', $this->syllabus),
            'statuses' => TopicCoverageStatus::cases(),
        ]);
    }

    private function trackId(): ?int
    {
        return $this->track === '' ? null : (int) $this->track;
    }
}
