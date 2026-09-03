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
     * Get the enrollments this person may read.
     *
     * @return Collection<int, StudentRecord>
     */
    public function enrollmentsFor(User $person): Collection
    {
        $ids = $this->childUserIds($person);
        $ids[] = $person->id;

        return StudentRecord::query()
            ->whereIn('user_id', array_unique($ids))
            ->whereIn('status', [EnrollmentStatus::Active, EnrollmentStatus::Graduated])
            ->with('user')
            ->orderBy('id')
            ->get();
    }

    /**
     * Check if the person may read this enrollment.
     */
    public function canRead(User $person, StudentRecord $enrollment): bool
    {
        if (!$this->isOpen($enrollment->school_id)) {
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
