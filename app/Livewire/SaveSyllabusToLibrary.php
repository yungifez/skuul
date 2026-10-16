<?php

namespace App\Livewire;

use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\CurriculumOutline;
use App\Models\Syllabus;
use App\Services\Syllabus\CurriculumLibraryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Keep a syllabus's weekly plan in the curriculum library for reuse.
 */
class SaveSyllabusToLibrary extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    public string $name = '';

    public function mount(): void
    {
        $this->name = $this->syllabus->name;
    }

    public function save(CurriculumLibraryService $library): void
    {
        Gate::authorize('view', $this->syllabus);
        Gate::authorize('create', CurriculumOutline::class);
        $this->validate(['name' => ['required', 'string', 'max:255']]);

        try {
            $library->saveFromSyllabus($this->syllabus, $this->name, $this->syllabus->description, auth()->user());
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("Saved {$this->name} to the curriculum library.");
    }

    public function render(): View
    {
        return view('livewire.save-syllabus-to-library');
    }
}
