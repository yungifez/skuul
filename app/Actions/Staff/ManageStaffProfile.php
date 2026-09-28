<?php

namespace App\Actions\Staff;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\School\EndSchoolMembership;
use App\Enums\AuditAction;
use App\Enums\LeaveStatus;
use App\Enums\StaffStatus;
use App\Exceptions\InvalidValueException;
use App\Models\StaffAvailability;
use App\Models\StaffCredential;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Keep one person's employment record in one school.
 */
class ManageStaffProfile
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private ManageStaffLeave $manageStaffLeave,
        private EndSchoolMembership $endSchoolMembership,
    ) {}

    /**
     * Write the employment record of a member of the working school.
     *
     * @param  array{user_id: int, staff_number?: string|null, job_title?: string|null, department?: string|null, employment_type: string, joined_on?: string|null}  $attributes
     *
     * A learner who moved keeps their membership at the campus they left, so
     * the membership alone does not make somebody staff.
     *
     * @throws InvalidValueException when the person is still a learner, already has a record here or the staff number is taken
     */
    public function create(array $attributes, ?User $actor = null): StaffProfile
    {
        $schoolId = current_school_id();

        if (StudentRecord::query()->where('user_id', $attributes['user_id'])->enrolled()->exists()) {
            throw new InvalidValueException('This person is still a learner. A learner cannot be made staff.');
        }

        try {
            return DB::transaction(function () use ($attributes, $schoolId, $actor): StaffProfile {
                if (StaffProfile::query()->where('school_id', $schoolId)->where('user_id', $attributes['user_id'])->exists()) {
                    throw new InvalidValueException('This person already has an employment record here.');
                }

                $this->refuseATakenStaffNumber($attributes['staff_number'] ?? null, $schoolId);

                $profile = StaffProfile::create(['school_id' => $schoolId, ...$attributes]);
                $this->auditor->record(AuditAction::StaffProfileChanged, $profile, ['change' => 'created'], $actor);

                return $profile;
            });
        } catch (UniqueConstraintViolationException) {
            // Somebody saved the same person or number a moment earlier.
            throw new InvalidValueException('This person or staff number was just given an employment record. Open the staff list to see it.');
        }
    }

    /**
     * Change the job, the hours, or the state of the record.
     *
     * A person who leaves gets a leaving date, today unless one is given. Any
     * leave they still hold after it is withdrawn, and their subjects, boarding
     * duty and cover end after it. A person who has not left has no leaving
     * date.
     *
     * @param  array{staff_number?: string|null, job_title?: string|null, department?: string|null, employment_type: string, status: string, left_on?: string|null}  $attributes
     *
     * @throws InvalidValueException when the dates or the staff number cannot stand
     */
    public function update(StaffProfile $profile, array $attributes, ?User $actor = null): StaffProfile
    {
        try {
            return DB::transaction(function () use ($profile, $attributes, $actor): StaffProfile {
                $profile = StaffProfile::query()->lockForUpdate()->findOrFail($profile->id);
                $this->refuseATakenStaffNumber($attributes['staff_number'] ?? null, $profile->school_id, $profile->id);

                $isLeaving = $attributes['status'] === StaffStatus::Left->value;
                $leftOn = $isLeaving ? Carbon::parse($attributes['left_on'] ?? now())->startOfDay() : null;

                if ($leftOn !== null && $profile->joined_on !== null && $leftOn->lt($profile->joined_on)) {
                    throw new InvalidValueException('A person cannot leave before they joined.');
                }

                $profile->fill([...$attributes, 'left_on' => $leftOn]);
                $changed = array_keys($profile->getDirty());

                if ($changed === []) {
                    return $profile;
                }

                $profile->save();
                $this->auditor->record(AuditAction::StaffProfileChanged, $profile, ['change' => 'updated', 'fields' => $changed], $actor);

                if ($leftOn !== null) {
                    $this->withdrawLeaveAfter($profile, $leftOn, $actor);
                    // They still work on their last day, and nothing after it.
                    $this->endSchoolMembership->endDutiesFrom($profile->user()->firstOrFail(), $profile->school_id, $leftOn->copy()->addDay());
                }

                return $profile;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('Another person was just given this staff number.');
        }
    }

    /**
     * Record what the person is qualified for.
     *
     * @param  array{type: string, name: string, issuer?: string|null, reference?: string|null, issued_on?: string|null, expires_on?: string|null}  $attributes
     */
    public function addCredential(StaffProfile $profile, array $attributes, ?User $actor = null): StaffCredential
    {
        return DB::transaction(function () use ($profile, $attributes, $actor): StaffCredential {
            $credential = StaffCredential::create(['staff_profile_id' => $profile->id, ...$attributes]);
            $this->auditor->record(AuditAction::StaffProfileChanged, $profile, ['change' => 'credential_added', 'credential_id' => $credential->id], $actor);

            return $credential;
        });
    }

    /**
     * Remove a qualification recorded by mistake.
     */
    public function removeCredential(StaffCredential $credential, ?User $actor = null): void
    {
        DB::transaction(function () use ($credential, $actor): void {
            $credential->delete();
            $this->auditor->record(AuditAction::StaffProfileChanged, $credential->staffProfile, ['change' => 'credential_removed', 'name' => $credential->name], $actor);
        });
    }

    /**
     * Record hours the person can work on one day.
     *
     * @throws InvalidValueException when the hours overlap hours already held that day
     */
    public function addHours(StaffProfile $profile, int $dayOfWeek, string $startsAt, string $endsAt, ?User $actor = null): StaffAvailability
    {
        return DB::transaction(function () use ($profile, $dayOfWeek, $startsAt, $endsAt, $actor): StaffAvailability {
            StaffProfile::query()->lockForUpdate()->findOrFail($profile->id);

            $overlap = $profile->availabilities()
                ->where('day_of_week', $dayOfWeek)
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->first();

            if ($overlap !== null) {
                throw new InvalidValueException('These hours overlap '.substr((string) $overlap->starts_at, 0, 5).' to '.substr((string) $overlap->ends_at, 0, 5).' on the same day.');
            }

            $hours = StaffAvailability::create([
                'staff_profile_id' => $profile->id,
                'day_of_week' => $dayOfWeek,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);
            $this->auditor->record(AuditAction::StaffProfileChanged, $profile, ['change' => 'hours_added', 'day_of_week' => $dayOfWeek], $actor);

            return $hours;
        });
    }

    /**
     * Remove hours the person no longer works.
     */
    public function removeHours(StaffAvailability $hours, ?User $actor = null): void
    {
        DB::transaction(function () use ($hours, $actor): void {
            $hours->delete();
            $this->auditor->record(AuditAction::StaffProfileChanged, $hours->staffProfile, ['change' => 'hours_removed', 'day_of_week' => $hours->day_of_week], $actor);
        });
    }

    /**
     * @throws InvalidValueException when another record in the school holds the number
     */
    private function refuseATakenStaffNumber(?string $staffNumber, int $schoolId, ?int $exceptProfileId = null): void
    {
        if ($staffNumber === null || $staffNumber === '') {
            return;
        }

        $taken = StaffProfile::query()
            ->where('school_id', $schoolId)
            ->whereRaw('LOWER(staff_number) = ?', [mb_strtolower($staffNumber)])
            ->when($exceptProfileId !== null, fn ($query) => $query->whereKeyNot($exceptProfileId))
            ->exists();

        if ($taken) {
            throw new InvalidValueException("Another person already has staff number {$staffNumber}.");
        }
    }

    /**
     * Withdraw leave that is still asked for or agreed and ends after the person left.
     */
    private function withdrawLeaveAfter(StaffProfile $profile, Carbon $leftOn, ?User $actor): void
    {
        $held = StaffLeaveRequest::query()
            ->where('staff_profile_id', $profile->id)
            ->whereIn('status', [LeaveStatus::Requested, LeaveStatus::Approved])
            ->whereDate('ends_on', '>', $leftOn)
            ->get();

        foreach ($held as $leave) {
            $this->manageStaffLeave->cancel($leave, $actor, 'The person left on '.$leftOn->format('j M Y').'.');
        }
    }
}
