<?php

namespace App\Console\Commands;

use App\Actions\Staff\ManageStaffProfile;
use App\Enums\SchoolMembershipStatus;
use App\Enums\StaffStatus;
use App\Exceptions\InvalidValueException;
use App\Models\SchoolMembership;
use App\Models\StaffProfile;
use Illuminate\Console\Command;

/**
 * End the campus access of staff whose last day has passed.
 *
 * A leaving day can be recorded ahead of time. The person works until that
 * day, and the day after it they must no longer sign in to the campus.
 */
class EndLeaversAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:end-leavers-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End the campus membership of staff whose last day has passed';

    /**
     * Execute the console command.
     */
    public function handle(ManageStaffProfile $staffProfiles): int
    {
        $ended = 0;
        $todayAt = [];

        // Every campus counts its own day. The query takes a day more than
        // the server's date, so a campus already in tomorrow is not missed.
        $profiles = StaffProfile::query()
            ->where('status', StaffStatus::Left->value)
            ->whereDate('left_on', '<', today()->addDay())
            ->whereExists(fn ($membership) => $membership->from((new SchoolMembership)->getTable())
                ->whereColumn('school_memberships.user_id', 'staff_profiles.user_id')
                ->whereColumn('school_memberships.school_id', 'staff_profiles.school_id')
                ->where('school_memberships.status', '!=', SchoolMembershipStatus::Ended->value))
            ->lazyById();

        foreach ($profiles as $profile) {
            if ($profile->left_on === null || !$profile->left_on->lt($todayAt[$profile->school_id] ??= school_today($profile->school_id))) {
                continue;
            }

            // The last person who can manage the campus stays until somebody
            // else can, or the campus could not be run at all.
            try {
                $staffProfiles->endAccessOfALeaver($profile);
                $ended++;
            } catch (InvalidValueException $exception) {
                $this->warn("Staff record {$profile->id}: {$exception->getMessage()}");
            }
        }

        $this->info("$ended leavers lost access.");

        return self::SUCCESS;
    }
}
