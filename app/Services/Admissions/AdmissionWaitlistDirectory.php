<?php

namespace App\Services\Admissions;

use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdmissionWaitlistDirectory
{
    /**
     * @return array{
     *     entries: Collection<int, AdmissionWaitlistEntry>,
     *     sections: Collection<int, AcademicCycleSection>,
     *     candidates: Collection<int, User>
     * }
     */
    public function forWorkingSchool(): array
    {
        return [
            'entries' => AdmissionWaitlistEntry::inSchool()
                ->with(['academicCycleSection.academicLevel', 'academicYear', 'candidate'])
                ->orderByDesc('priority')
                ->orderBy('position')
                ->get(),
            'sections' => AcademicCycleSection::inSchool()
                ->with(['academicLevel', 'academicYear'])
                ->where('status', 'active')
                ->whereNotNull('capacity')
                ->orderBy('academic_year_id')
                ->orderBy('academic_level_id')
                ->orderBy('position')
                ->get(),
            'candidates' => User::ofSchool()
                ->whereDoesntHave('studentRecords', function ($query): void {
                    $query->where('school_id', current_school_id())
                        ->where('status', 'active');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ];
    }
}
