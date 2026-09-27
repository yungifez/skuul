<?php

namespace App\Livewire;

use App\Actions\Report\PublishTranscript;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreTranscriptRequest;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\StudentRecord;
use App\Models\TranscriptSnapshot;
use App\Services\Report\TranscriptDirectory as TranscriptDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TranscriptDirectory extends Component
{
    use DispatchesStatusNotifications;
    use WithPagination;

    #[Url(as: 'student_record_id', except: '')]
    public string $studentRecordId = '';

    public ?int $student_record_id = null;

    public string $reason = '';

    protected TranscriptDirectoryService $directory;

    public function boot(TranscriptDirectoryService $directory): void
    {
        Gate::authorize('viewAny', TranscriptSnapshot::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        if (!ctype_digit($this->studentRecordId) || (int) $this->studentRecordId < 1) {
            $this->studentRecordId = '';
        }
    }

    public function updatedStudentRecordId(): void
    {
        if (!ctype_digit($this->studentRecordId) || (int) $this->studentRecordId < 1) {
            $this->studentRecordId = '';
        }

        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->studentRecordId = '';
        $this->resetPage();
    }

    public function issueTranscript(PublishTranscript $publishTranscript): void
    {
        Gate::authorize('create', TranscriptSnapshot::class);

        $validated = $this->validate(StoreTranscriptRequest::transcriptRules());
        $student = StudentRecord::inSchool()->findOrFail($validated['student_record_id']);

        try {
            $publishTranscript->publish(
                $student,
                auth()->user(),
                filled($validated['reason'] ?? null) ? $validated['reason'] : null,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('transcript', $exception->getMessage());

            return;
        }

        $this->reset(['student_record_id', 'reason']);
        $this->notify('Transcript issued.');
    }

    public function render(): View
    {
        return ViewFactory::make('livewire.transcript-directory', [
            'transcripts' => $this->directory->transcripts(
                $this->studentRecordId === '' ? null : (int) $this->studentRecordId,
            ),
            'students' => $this->directory->students(),
            'selectedStudent' => $this->studentRecordId === '' ? null : (int) $this->studentRecordId,
        ]);
    }
}
