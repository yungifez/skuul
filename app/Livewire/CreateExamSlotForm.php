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
 * Add a paper to an exam.
 */
class CreateExamSlotForm extends Component
{
    #[Locked]
    public Exam $exam;

    public string $name = '';

    public string $description = '';

    public string $totalMarks = '100';

    public function mount(Exam $exam): void
    {
        Gate::authorize('createForExam', [ExamSlot::class, $exam]);

        $this->exam = $exam;
    }

    public function save(SaveExamSlot $saveExamSlot): void
    {
        Gate::authorize('createForExam', [ExamSlot::class, $this->exam]);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'totalMarks' => ['required', 'integer', 'min:1', 'max:1000'],
        ], [], ['totalMarks' => 'highest mark']);

        try {
            $saveExamSlot->create($this->exam, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'total_marks' => (int) $this->totalMarks,
            ]);
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', "{$this->name} was added to {$this->exam->name}.");
        $this->redirectRoute('exam-slots.index', $this->exam);
    }

    public function render(): View
    {
        return view('livewire.create-exam-slot-form');
    }
}
