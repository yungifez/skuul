<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use Illuminate\View\View;
use Livewire\Component;

class CreateNoticeForm extends Component
{
    public function render(): View
    {
        return view('livewire.create-notice-form', [
            'sections' => AcademicCycleSection::query()
                ->inSchool()
                ->where('status', AcademicStructureStatus::Active)
                ->with('academicLevel:id,name')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
