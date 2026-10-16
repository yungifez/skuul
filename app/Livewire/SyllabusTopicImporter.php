<?php

namespace App\Livewire;

use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Syllabus;
use App\Services\Syllabus\CurriculumLibraryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Copy topics into a draft from the curriculum library or from an earlier syllabus.
 */
class SyllabusTopicImporter extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    /** The chosen source, as "outline:{id}" or "syllabus:{id}". */
    public string $source = '';

    public function copy(CurriculumLibraryService $library): void
    {
        Gate::authorize('update', $this->syllabus);
        $this->validate(['source' => ['required', 'regex:/^(outline|syllabus):\d+$/']], ['source.required' => 'Choose where to copy the topics from.']);

        [$kind, $id] = explode(':', $this->source);
        $source = ($kind === 'outline' ? $library->outlinesFor($this->syllabus) : $library->earlierSyllabiFor($this->syllabus))
            ->firstWhere('id', (int) $id);

        if ($source === null) {
            $this->notify('Copy from an outline or syllabus of the same subject in this school.', 'danger');

            return;
        }

        try {
            $count = $library->copyInto($this->syllabus, $source);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', trans_choice('Copied :count topic. Change them as needed for this class.|Copied :count topics. Change them as needed for this class.', $count));
        $this->redirectRoute('syllabi.edit', $this->syllabus);
    }

    public function render(CurriculumLibraryService $library): View
    {
        return view('livewire.syllabus-topic-importer', [
            'outlines' => $library->outlinesFor($this->syllabus),
            'earlierSyllabi' => $library->earlierSyllabiFor($this->syllabus),
        ]);
    }
}
