<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class AssignTeacherToSubjects extends Component
{
    /** @var array<int, array{id: int, name: string}> */
    public array $teachers = [];

    /** @var array<int, array{id: int, name: string, short_name: string, assigned: bool}> */
    public array $subjects = [];

    public ?int $teacherId = null;

    public ?int $teacherStateId = null;

    public function mount(): void
    {
        $this->teachers = User::ofSchool()
            ->role(Role::Teacher->value)
            ->orderBy('name')
            ->get(['users.id', 'users.name'])
            ->map(fn (User $teacher): array => ['id' => $teacher->id, 'name' => $teacher->name])
            ->all();
        $this->teacherId = $this->teachers[0]['id'] ?? null;
    }

    public function loadSubjects(): void
    {
        $this->validate(['teacherId' => ['required', 'integer']]);

        $teacher = User::ofSchool()->role(Role::Teacher->value)->findOrFail($this->teacherId);
        $this->teacherStateId = $teacher->id;
        $this->subjects = Subject::inSchool()
            ->with('teachers:id')
            ->orderBy('name')
            ->get(['id', 'name', 'short_name'])
            ->map(fn (Subject $subject): array => [
                'id' => $subject->id,
                'name' => $subject->name,
                'short_name' => $subject->short_name,
                'assigned' => $subject->teachers->contains('id', $teacher->id),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.assign-teacher-to-subjects');
    }
}
