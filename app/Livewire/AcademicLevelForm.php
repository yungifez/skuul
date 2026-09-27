<?php

namespace App\Livewire;

use App\Actions\Curriculum\CreateAcademicLevel;
use App\Actions\Curriculum\UpdateAcademicLevel;
use App\Enums\AcademicStructureStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Add a reusable class level or level group, or change one.
 */
class AcademicLevelForm extends Component
{
    #[Locked]
    public ?AcademicLevel $academicLevel = null;

    /**
     * Where a save opened from a setup screen returns to.
     */
    #[Locked]
    public bool $setup = false;

    #[Locked]
    public bool $schoolSetup = false;

    #[Locked]
    public ?int $academicYearId = null;

    public string $name = '';

    public string $code = '';

    public string $position = '0';

    public bool $isGroup = false;

    public string $parentId = '';

    public function mount(
        ?AcademicLevel $academicLevel = null,
        bool $setup = false,
        bool $schoolSetup = false,
        ?int $academicYearId = null,
        ?int $preselectedParentId = null,
    ): void {
        if ($academicLevel?->exists !== true) {
            Gate::authorize('create', AcademicLevel::class);

            $this->setup = $setup;
            $this->schoolSetup = $setup && $schoolSetup;
            $this->academicYearId = $setup && $academicYearId !== null
                ? AcademicYear::inSchool()->whereKey($academicYearId)->value('id')
                : null;

            if ($preselectedParentId !== null && $this->parentOptions()->contains('id', $preselectedParentId)) {
                $this->parentId = (string) $preselectedParentId;
            }

            return;
        }

        Gate::authorize('update', $academicLevel);

        $this->academicLevel = $academicLevel;
        $this->name = $academicLevel->name;
        $this->code = (string) $academicLevel->code;
        $this->position = (string) $academicLevel->position;
        $this->isGroup = $academicLevel->is_group;
        $this->parentId = $academicLevel->parent_id === null ? '' : (string) $academicLevel->parent_id;
    }

    /**
     * A group sits at the top, so choosing one clears the group above it.
     */
    public function updatedIsGroup(): void
    {
        if ($this->isGroup) {
            $this->parentId = '';
        }
    }

    public function save(CreateAcademicLevel $createAcademicLevel, UpdateAcademicLevel $updateAcademicLevel): void
    {
        $academicLevel = $this->academicLevel;

        if ($academicLevel === null) {
            Gate::authorize('create', AcademicLevel::class);
        } else {
            Gate::authorize('update', $academicLevel);
        }

        $this->name = trim($this->name);
        $this->code = trim($this->code);

        $ofThisSchool = fn ($query) => $query->where('school_id', current_school_id());

        $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('academic_levels', 'name')->where($ofThisSchool)->ignore($academicLevel?->id)],
            'code' => ['nullable', 'string', 'max:100', Rule::unique('academic_levels', 'code')->where($ofThisSchool)->ignore($academicLevel?->id)],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'isGroup' => ['boolean'],
            'parentId' => [
                'nullable',
                Rule::prohibitedIf($this->isGroup),
                Rule::in($this->parentOptions()->pluck('id')->map(fn (int $id): string => (string) $id)->all()),
            ],
        ], [
            'name.unique' => 'This school already has a level with that name.',
            'code.unique' => 'This school already has a level with that short code.',
            'parentId.prohibited' => 'A level group sits at the top, so it cannot belong to another group.',
            'parentId.in' => 'Choose one of the listed level groups.',
        ], [
            'code' => 'short code',
            'position' => 'display order',
            'parentId' => 'level group',
        ]);

        $parent = $this->parentId === '' ? null : AcademicLevel::inSchool()->findOrFail((int) $this->parentId);
        $code = $this->code === '' ? null : $this->code;

        try {
            if ($academicLevel === null) {
                $created = $createAcademicLevel->create($this->name, $code, $parent, (int) $this->position, auth()->user(), $this->isGroup);
            } else {
                $updateAcademicLevel->update($academicLevel, [
                    'name' => $this->name,
                    'code' => $code,
                    'position' => (int) $this->position,
                    'is_group' => $this->isGroup,
                ], $parent, auth()->user());
            }
        } catch (InvalidValueException $exception) {
            $this->addError('isGroup', $exception->getMessage());

            return;
        }

        if ($academicLevel !== null) {
            session()->flash('success', 'Academic level updated.');
            $this->redirectRoute('academic-levels.show', $academicLevel);

            return;
        }

        $this->returnAfterCreating($created);
    }

    public function render(): View
    {
        return view('livewire.academic-level-form', [
            'parentOptions' => $this->parentOptions(),
        ]);
    }

    private function returnAfterCreating(AcademicLevel $created): void
    {
        if ($this->schoolSetup) {
            session()->flash('success', 'Class created. Review it and create this year’s sections.');
            $this->redirectRoute('schools.setup', [current_school(), 'classes']);

            return;
        }

        if ($this->setup && $this->academicYearId !== null) {
            session()->flash('success', 'Class created. Continue by building this year’s classes.');
            $this->redirectRoute('academic-years.setup', [$this->academicYearId, 'structure']);

            return;
        }

        if ($this->setup) {
            session()->flash('success', 'Class created. Continue by setting up an academic year.');
            $this->redirectRoute('schools.setup', [current_school(), 'academic-year']);

            return;
        }

        session()->flash('success', 'Academic level created. Add a cycle section to use it in a cycle.');
        $this->redirectRoute('academic-levels.show', $created);
    }

    /**
     * The active groups a level may sit under. A level keeps its current group in the list.
     *
     * @return Collection<int, AcademicLevel>
     */
    private function parentOptions(): Collection
    {
        $academicLevel = $this->academicLevel;

        return AcademicLevel::inSchool()
            ->where(function (Builder $query) use ($academicLevel): void {
                $query->where(fn (Builder $groups) => $groups->where('is_group', true)->where('status', AcademicStructureStatus::Active));

                if ($academicLevel?->parent_id !== null) {
                    $query->orWhereKey($academicLevel->parent_id);
                }
            })
            ->when($academicLevel !== null, fn (Builder $query) => $query->whereKeyNot($academicLevel->id))
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
