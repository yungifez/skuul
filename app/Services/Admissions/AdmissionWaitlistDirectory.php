<?php

namespace App\Services\Admissions;

use App\Enums\AcademicStructureStatus;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\User;
use App\Services\Academic\SectionSeats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AdmissionWaitlistDirectory
{
    public function __construct(private SectionSeats $seats) {}

    /**
     * Get the queue, the sections a candidate can wait for, and the people who can wait.
     *
     * A section is offered only while it is full and its cycle is open, and a
     * person only while they are not enrolled here and do not work as staff.
     * These are the checks that joining the queue makes. The next waiting
     * candidate of a section with a free seat is the one who can be offered it.
     *
     * @return array{
     *     entries: Collection<int, AdmissionWaitlistEntry>,
     *     nextEntryIds: list<int>,
     *     sections: Collection<int, AcademicCycleSection>,
     *     candidates: Collection<int, User>
     * }
     */
    public function forWorkingSchool(): array
    {
        $entries = AdmissionWaitlistEntry::inSchool()
            ->with(['academicCycleSection.academicLevel', 'academicYear', 'candidate'])
            ->orderByDesc('priority')
            ->orderBy('position')
            ->get();

        return [
            'entries' => $entries,
            'nextEntryIds' => $entries
                ->where('status', AdmissionWaitlistStatus::Pending)
                ->unique('academic_cycle_section_id')
                ->reject(fn (AdmissionWaitlistEntry $entry): bool => $this->seats->isFull($entry->academicCycleSection))
                ->pluck('id')
                ->values()
                ->all(),
            'sections' => AcademicCycleSection::inSchool()
                ->with(['academicLevel', 'academicYear'])
                ->where('status', AcademicStructureStatus::Active)
                ->whereNotNull('capacity')
                ->orderBy('academic_year_id')
                ->orderBy('academic_level_id')
                ->orderBy('position')
                ->get()
                ->filter(fn (AcademicCycleSection $section): bool => !$section->academicYear->isClosed() && $this->seats->isFull($section))
                ->values(),
            'candidates' => User::ofSchool()
                ->notStaff()
                ->whereDoesntHave('studentRecords', fn (Builder $enrollment) => $enrollment
                    ->where('school_id', current_school_id())
                    ->whereIn('status', EnrollmentStatus::enrolled()))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ];
    }
}
