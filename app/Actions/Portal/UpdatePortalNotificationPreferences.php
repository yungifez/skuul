<?php

namespace App\Actions\Portal;

use App\Exceptions\InvalidValueException;
use App\Models\NoticeNotificationPreference;
use App\Models\User;
use App\Services\Portal\PortalAccess;
use Illuminate\Support\Facades\DB;

class UpdatePortalNotificationPreferences
{
    public function __construct(private PortalAccess $portalAccess) {}

    /**
     * Save the notice-email choices for the campuses this person can read.
     *
     * @param  array<int|string, bool|int|string>  $preferences
     *
     * @throws InvalidValueException when a campus is outside the person's portal
     */
    public function update(User $person, array $preferences): void
    {
        $allowedSchoolIds = $this->portalAccess
            ->notificationSchoolsFor($person)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $submittedSchoolIds = array_map('intval', array_keys($preferences));

        if (array_diff($submittedSchoolIds, $allowedSchoolIds) !== []) {
            throw new InvalidValueException('You can only change notification settings for your own schools.');
        }

        DB::transaction(function () use ($person, $preferences): void {
            foreach ($preferences as $schoolId => $emailEnabled) {
                NoticeNotificationPreference::updateOrCreate(
                    [
                        'user_id' => $person->id,
                        'school_id' => (int) $schoolId,
                    ],
                    ['email_enabled' => (bool) $emailEnabled],
                );
            }
        });
    }
}
