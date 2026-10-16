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
 * Authorize students through their enrollments and guardians through recorded child links.
 * Portal access is independent of staff permissions.
 */
class PortalAccess
{
    /**
     * The campus tool each family page also needs, keyed by portal area.
     *
     * @var array<string, Feature>
     */
    public const AREA_FEATURES = [
        'attendance' => Feature::Attendance,
        'calendar' => Feature::Events,
        'library' => Feature::Library,
        'boarding' => Feature::Boarding,
        'graduation' => Feature::GraduationPlans,
        'programmes' => Feature::Programmes,
    ];

    /**
     * Keep graduated, transferred, withdrawn, and suspended enrollments available to families.
     * Exclude archived enrollments.
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

        $feature = self::AREA_FEATURES[$area->value] ?? null;

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
