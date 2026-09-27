<?php

namespace App\Livewire;

use App\Enums\SyllabusStatus;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Show a syllabus as a weekly plan, with the actions its status allows.
 */
class ShowSyllabus extends Component
{
    public Syllabus $syllabus;

    /**
     * Send the attached PDF to a reader the policy lets see this syllabus.
     */
    public function download(): StreamedResponse
    {
        Gate::authorize('view', $this->syllabus);

        abort_if($this->syllabus->file === null || !Storage::disk('public')->exists($this->syllabus->file), 404);

        return Storage::disk('public')->download($this->syllabus->file, Str::slug($this->syllabus->name).'.pdf');
    }

    public function render(): View
    {
        $currentWeek = $this->syllabus->teachingWeekOn();

        return view('livewire.show-syllabus', [
            'topicsByWeek' => $this->syllabus->topics->groupBy(fn (SyllabusTopic $topic): string => $topic->week === null ? 'Unscheduled' : (string) $topic->week),
            'currentWeek' => $currentWeek,
            'openRevision' => $this->syllabus->status === SyllabusStatus::Published ? $this->syllabus->openRevision() : null,
            'replacement' => $this->syllabus->status === SyllabusStatus::Superseded
                ? $this->syllabus->revisions()->where('status', '!=', SyllabusStatus::Draft)->latest('revision')->first()
                : null,
        ]);
    }
}
