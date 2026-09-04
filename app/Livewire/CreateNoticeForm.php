<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Enums\NoticeAudienceScope;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use Illuminate\View\View;
use Livewire\Component;

class CreateNoticeForm extends Component
{
    public function render(): View
    {
        return view('livewire.create-notice-form', [
            'academicLevels' => AcademicLevel::query()
                ->inSchool()
                ->where('status', AcademicStructureStatus::Active)
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'is_group', 'name']),
            'audienceScopes' => NoticeAudienceScope::cases(),
            'sections' => AcademicCycleSection::query()
                ->inSchool()
                ->where('status', AcademicStructureStatus::Active)
                ->with('academicLevel:id,name')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
