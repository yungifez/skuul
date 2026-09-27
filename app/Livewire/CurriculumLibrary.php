<?php

namespace App\Livewire;

use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\CurriculumOutline;
use App\Models\Subject;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Browse the school's reusable schemes of work.
 */
class CurriculumLibrary extends Component
{
    use DispatchesStatusNotifications;

    #[Url(as: 'subject')]
    public string $subjectId = '';

    public ?int $openOutlineId = null;

    public function mount(): void
    {
        Gate::authorize('viewAny', CurriculumOutline::class);
    }

    public function toggle(int $outlineId): void
    {
        $this->openOutlineId = $this->openOutlineId === $outlineId ? null : $outlineId;
    }

    public function delete(int $outlineId): void
    {
        $outline = CurriculumOutline::query()->inSchool()->findOrFail($outlineId);
        Gate::authorize('delete', $outline);

        $outline->delete();
        $this->notify("Removed {$outline->name} from the library. Syllabi copied from it keep their topics.");
    }

    public function render(): View
    {
        $outlines = CurriculumOutline::query()
            ->inSchool()
            ->when($this->subjectId !== '', fn ($query) => $query->where('subject_id', (int) $this->subjectId))
            ->with(['subject:id,name', 'academicLevel:id,name', 'createdBy:id,name'])
            ->withCount('topics')
            ->orderBy('name')
            ->get();

        return view('livewire.curriculum-library', [
            'outlines' => $outlines,
            'openTopics' => $this->openOutlineId === null
                ? collect()
                : $outlines->firstWhere('id', $this->openOutlineId)?->topics()->get() ?? collect(),
            'subjects' => Subject::query()->inSchool()->whereIn('id', CurriculumOutline::query()->inSchool()->select('subject_id'))->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
