<?php

namespace App\Livewire;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Curriculum\ChangeCourseOfferingStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The subjects being taught, with a way to activate a draft and add a teacher.
 */
class CourseOfferingDirectory extends Component
{
    use DispatchesStatusNotifications;
    use WithPagination;

    #[Url(as: 'academic_year_id')]
    public string $academicYearId = '';

    #[Url(as: 'subject_id')]
    public string $subjectId = '';

    /** The offering whose teacher form is open. */
    public ?int $assigningId = null;

    public string $teacherId = '';

    public string $role = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', CourseOffering::class);

        $this->role = TeachingRole::Lead->value;
    }

    public function updatedAcademicYearId(): void
    {
        $this->resetPage();
    }

    public function updatedSubjectId(): void
    {
        $this->resetPage();
    }

    public function activate(int $courseOfferingId, ChangeCourseOfferingStatus $changeCourseOfferingStatus): void
    {
        $courseOffering = $this->courseOffering($courseOfferingId);
        Gate::authorize('update', $courseOffering);

        try {
            $changeCourseOfferingStatus->change($courseOffering, CourseOfferingStatus::Active, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("{$courseOffering->subject->name} is now active.");
    }

    public function startAssigning(int $courseOfferingId): void
    {
        Gate::authorize('update', $this->courseOffering($courseOfferingId));

        $this->resetErrorBag();
        $this->reset('teacherId');
        $this->role = TeachingRole::Lead->value;
        $this->assigningId = $courseOfferingId;
    }

    public function assignTeacher(AssignTeacher $assignTeacher): void
    {
        $courseOffering = $this->courseOffering((int) $this->assigningId);
        Gate::authorize('update', $courseOffering);

        $this->validate([
            'teacherId' => ['required', 'integer', Rule::in($this->teachers()->pluck('id')->all())],
            'role' => ['required', Rule::enum(TeachingRole::class)],
        ], ['teacherId.required' => 'Choose a teacher.', 'teacherId.in' => 'Choose a teacher of this school.']);

        $teacher = User::ofSchool()->findOrFail((int) $this->teacherId);

        try {
            $assignTeacher->assign($courseOffering, $teacher, TeachingRole::from($this->role), actor: auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('teacherId', $exception->getMessage());

            return;
        }

        $this->cancelAssigning();
        $this->notify("{$teacher->name} now teaches {$courseOffering->subject->name}.");
    }

    public function cancelAssigning(): void
    {
        $this->reset('assigningId', 'teacherId');
        $this->resetErrorBag();
    }

    public function render(): View
    {
        $academicYearId = ctype_digit($this->academicYearId) ? (int) $this->academicYearId : 0;
        $subjectId = ctype_digit($this->subjectId) ? (int) $this->subjectId : 0;

        return view('livewire.course-offering-directory', [
            'courseOfferings' => CourseOffering::inSchool()
                ->with([
                    'academicLevel:id,name,is_group',
                    'academicPeriod:id,name,label',
                    'academicYear:id,start_year,stop_year',
                    'cycleSections:id,name,label',
                    'studentRecords.user:id,name',
                    'subject:id,name,short_name',
                    'teachingAssignments.teacher:id,name',
                ])
                ->when($academicYearId > 0, fn (Builder $query): Builder => $query->where('academic_year_id', $academicYearId))
                ->when($subjectId > 0, fn (Builder $query): Builder => $query->where('subject_id', $subjectId))
                ->latest()
                ->paginate(25),
            'academicYears' => AcademicYear::inSchool()->orderByDesc('start_year')->get(),
            'subjects' => Subject::inSchool()->orderBy('name')->get(['id', 'name']),
            'teachers' => $this->assigningId === null ? collect() : $this->teachers(),
            'roles' => TeachingRole::cases(),
        ]);
    }

    private function courseOffering(int $courseOfferingId): CourseOffering
    {
        return CourseOffering::inSchool()->with('subject:id,name')->findOrFail($courseOfferingId);
    }

    /**
     * @return Collection<int, User>
     */
    private function teachers(): Collection
    {
        return User::ofSchool()->role(Role::Teacher->value)->orderBy('users.name')->get(['users.id', 'users.name']);
    }
}
