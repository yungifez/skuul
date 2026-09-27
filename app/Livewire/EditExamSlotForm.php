<?php

namespace App\Livewire;

use App\Actions\Exam\SaveExamSlot;
use App\Exceptions\InvalidValueException;
use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Rename a paper, describe it again, or change its highest mark.
 */
class EditExamSlotForm extends Component
{
    #[Locked]
    public Exam $exam;

    #[Locked]
    public ExamSlot $examSlot;

    public string $name = '';

    public string $description = '';

    public string $totalMarks = '';

    public function mount(Exam $exam, ExamSlot $examSlot): void
    {
        abort_unless($examSlot->exam_id === $exam->id, 404);
        Gate::authorize('update', $examSlot);

        $this->exam = $exam;
        $this->examSlot = $examSlot;
        $this->name = $examSlot->name;
        $this->description = (string) $examSlot->description;
        $this->totalMarks = (string) $examSlot->total_marks;
    }

    public function save(SaveExamSlot $saveExamSlot): void
    {
        Gate::authorize('update', $this->examSlot);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'totalMarks' => ['required', 'integer', 'min:1', 'max:1000'],
        ], [], ['totalMarks' => 'highest mark']);

        try {
            $this->examSlot = $saveExamSlot->update($this->examSlot, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'total_marks' => (int) $this->totalMarks,
            ]);
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The paper was saved.');
        $this->redirectRoute('exam-slots.index', $this->exam);
    }

    public function render(): View
    {
        return view('livewire.edit-exam-slot-form');
    }
}
