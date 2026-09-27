<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Enums\SyllabusStatus;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Support\Collection;
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

    public function mount(): void
    {
        Gate::authorize('view', $this->syllabus);
    }

    /**
     * Send the attached PDF to a reader the policy lets see this syllabus.
     */
    public function download(): StreamedResponse
    {
        Gate::authorize('view', $this->syllabus);

        abort_if($this->syllabus->file === null || !Storage::disk('public')->exists($this->syllabus->file), 404);

        return Storage::disk('public')->download($this->syllabus->file, Str::slug($this->syllabus->name).'.pdf');
    }

    public function render(SyllabusCoverageService $coverage): View
    {
        $currentWeek = $this->syllabus->teachingWeekOn();
        $user = auth()->user();
        $isStudent = $user?->hasRole(Role::Student) ?? false;

        return view('livewire.show-syllabus', [
            'topicsByWeek' => $this->syllabus->topics->groupBy(fn (SyllabusTopic $topic): string => $topic->week === null ? 'Unscheduled' : (string) $topic->week),
            'currentWeek' => $currentWeek,
            'openRevision' => $this->syllabus->status === SyllabusStatus::Published ? $this->syllabus->openRevision() : null,
            'replacement' => $this->syllabus->status === SyllabusStatus::Superseded
                ? $this->syllabus->revisions()->where('status', '!=', SyllabusStatus::Draft)->latest('revision')->first()
                : null,
            'showTracker' => !$isStudent && $this->syllabus->status !== SyllabusStatus::Draft && $this->syllabus->topics->isNotEmpty(),
            'studentCoverage' => $isStudent ? $this->studentCoverage($coverage) : collect(),
        ]);
    }

    /**
     * Get what the signed-in student's class was taught, keyed by topic id.
     *
     * @return Collection<int, SyllabusTopicCoverage>
     */
    private function studentCoverage(SyllabusCoverageService $coverage): Collection
    {
        $sectionId = auth()->user()?->studentRecord()->attending()->value('academic_cycle_section_id');
        $track = in_array($sectionId, array_column($coverage->tracks($this->syllabus), 'id'), true) ? $sectionId : null;

        return $coverage->coverageFor($this->syllabus, $track);
    }
}
