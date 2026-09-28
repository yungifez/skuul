<?php

namespace App\Services\Portal;

use App\Enums\EnrollmentStatus;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\ParentRecord;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Say whose records a person may read in the portal, and which areas are open.
 *
 * A student reads their own enrollments. A guardian reads the enrollments of
 * the children they are recorded against. Nobody reads anything else, and no
 * portal read ever depends on a staff permission.
 */
class PortalAccess
{
    /**
     * The enrollments a family keeps reading.
     *
     * A learner who graduated, transferred, or was withdrawn still has report
     * cards, transcripts, and perhaps a debt at that school. The family needs
     * them to enrol elsewhere and to settle up, and has a right to see them.
     * A suspended learner is still enrolled, and the family needs the
     * school's notices most then. An archived enrollment was put away by the
     * school, and stays out.
     */
    private const ReadableStatuses = [
        EnrollmentStatus::Active,
        EnrollmentStatus::Suspended,
        EnrollmentStatus::Graduated,
        EnrollmentStatus::Transferred,
        EnrollmentStatus::Withdrawn,
    ];

    /**
     * Get the enrollments this person may read.
     *
     * @return Collection<int, StudentRecord>
     */
    public function enrollmentsFor(User $person): Collection
    {
        $ids = $this->childUserIds($person);
        $ids[] = $person->id;

        // An enrollment the upgrade could not place at a campus has no
        // school to open a portal for, so it is left out until staff place it.
        return StudentRecord::query()
            ->whereIn('user_id', array_unique($ids))
            ->whereNotNull('school_id')
            ->whereIn('status', self::ReadableStatuses)
            ->with('user')
            ->orderBy('id')
            ->get();
    }

    /**
     * Check if the person may read this enrollment.
     */
    public function canRead(User $person, StudentRecord $enrollment): bool
    {
        if (!in_array($enrollment->status, self::ReadableStatuses, true)) {
            return false;
        }

        if ($enrollment->school_id === null || !$this->isOpen($enrollment->school_id)) {
            return false;
        }

        if ($enrollment->user_id === $person->id) {
            return true;
        }

        return in_array($enrollment->user_id, $this->childUserIds($person), true);
    }

    /**
     * Check if the portal is open at all.
     */
    public function isOpen(?int $schoolId = null): bool
    {
        return features()->enabled(Feature::Portal, $schoolId);
    }

    /**
     * Check if one area of the portal is open.
     *
     * An area a school has not chosen is open, because a school that turns the
     * portal on means the portal it already knows.
     */
    public function areaIsOpen(PortalArea $area, ?int $schoolId = null): bool
    {
        if (!$this->isOpen($schoolId)) {
            return false;
        }

        $feature = match ($area) {
            PortalArea::Attendance => Feature::Attendance,
            PortalArea::Calendar => Feature::Events,
            PortalArea::Library => Feature::Library,
            PortalArea::Boarding => Feature::Boarding,
            PortalArea::Graduation => Feature::GraduationPlans,
            PortalArea::Programmes => Feature::Programmes,
            default => null,
        };

        if ($feature !== null && features()->disabled($feature, $schoolId)) {
            return false;
        }

        return (bool) features()->config(Feature::Portal, $area->value, true, $schoolId);
    }

    /**
     * Get the campuses where this person may manage notice delivery.
     *
     * @return SupportCollection<int, School>
     */
    public function notificationSchoolsFor(User $person): SupportCollection
    {
        return $this->enrollmentsFor($person)
            ->load('school:id,name')
            ->filter(fn (StudentRecord $enrollment): bool => $this->areaIsOpen(PortalArea::Notices, $enrollment->school_id))
            ->unique('school_id')
            ->pluck('school')
            ->values();
    }

    /**
     * Get the accounts of the children this person is recorded against.
     *
     * @return array<int, int>
     */
    private function childUserIds(User $person): array
    {
        /** @var ParentRecord|null $parentRecord */
        $parentRecord = $person->parentRecord;

        if ($parentRecord === null) {
            return [];
        }

        return $parentRecord->students()->pluck('users.id')->all();
    }
}
