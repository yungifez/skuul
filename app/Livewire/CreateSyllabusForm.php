<?php

namespace App\Livewire;

use App\Enums\CourseOfferingStatus;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class CreateSyllabusForm extends Component
{
    /** @var Collection<int, CourseOffering> */
    public Collection $courseOfferings;

    public function mount(): void
    {
        $this->courseOfferings = CourseOffering::inSchool()
            ->where('status', '!=', CourseOfferingStatus::Archived)
            ->when(!auth()->user()?->can('approve syllabus'), fn (Builder $offerings): Builder => $offerings->whereHas(
                'teachingAssignments',
                fn (Builder $assignments): Builder => $assignments->where('user_id', auth()->id()),
            ))
            ->with(['subject:id,name,short_name', 'academicPeriod:id,name,label', 'academicLevel:id,name'])
            ->orderByDesc('academic_year_id')
            ->orderBy('academic_level_id')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.create-syllabus-form');
    }
}
