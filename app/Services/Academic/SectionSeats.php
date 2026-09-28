<?php

namespace App\Services\Academic;

use App\Enums\AdmissionWaitlistStatus;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\StudentRecord;

/**
 * Count the seats of a home section.
 *
 * A seat is taken by a learner who attends, suspended learners included, and
 * by a waitlisted family that was offered it and has not answered. Placement,
 * re-admission, the waitlist and the section form all ask here, so they can
 * never disagree about whether a section is full.
 *
 * Callers lock the section row first. The count is then true until their
 * transaction ends.
 */
class SectionSeats
{
    /**
     * Count the seats taken.
     *
     * @param  StudentRecord|null  $except  a learner asking for their own seat back, left out of the count
     * @param  AdmissionWaitlistEntry|null  $exceptOffer  an offer being accepted, which fills its own seat
     */
    public function taken(
        AcademicCycleSection $section,
        ?StudentRecord $except = null,
        ?AdmissionWaitlistEntry $exceptOffer = null,
    ): int {
        $learners = StudentRecord::query()
            ->where('academic_cycle_section_id', $section->id)
            ->enrolled()
            ->when($except !== null, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->count();

        $offers = AdmissionWaitlistEntry::query()
            ->where('academic_cycle_section_id', $section->id)
            ->where('status', AdmissionWaitlistStatus::Offered)
            ->when($exceptOffer !== null, fn ($query) => $query->whereKeyNot($exceptOffer->getKey()))
            ->count();

        return $learners + $offers;
    }

    /**
     * Check if the section has no seat left.
     *
     * A section without a capacity is never full.
     */
    public function isFull(
        AcademicCycleSection $section,
        ?StudentRecord $except = null,
        ?AdmissionWaitlistEntry $exceptOffer = null,
    ): bool {
        return $section->capacity !== null && $this->taken($section, $except, $exceptOffer) >= $section->capacity;
    }
}
