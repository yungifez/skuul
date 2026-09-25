<?php

namespace App\Livewire;

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
    use WithPagination;

    #[Url(as: 'student_record_id', except: '')]
    public string $studentRecordId = '';

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
