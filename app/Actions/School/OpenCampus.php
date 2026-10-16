<?php

namespace App\Actions\School;

use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\User;
use App\Services\School\SchoolService;
use Illuminate\Support\Facades\DB;

/**
 * Add a campus to an organization and let its founder work in it.
 *
 * The campus starts with the default operating language. The person who
 * opened it joins it without making it their primary campus, so setup can
 * continue there.
 */
class OpenCampus
{
    public function __construct(
        private SchoolService $schoolService,
        private GrantSchoolMembership $grantSchoolMembership,
    ) {}

    /**
     * @param  array{name: string, address: string, country: string, state: string, city: string, postal_code: string, phone?: string|null, email?: string|null, initials?: string|null, logo?: mixed}  $details
     */
    public function open(Organization $organization, array $details, User $founder): School
    {
        return DB::transaction(function () use ($organization, $details, $founder): School {
            $school = $this->schoolService->createSchool([...$details, 'organization_id' => $organization->id]);
            $school->operatingProfile()->firstOrCreate([], [
                'preset' => SchoolOperatingProfile::DEFAULT_PRESET,
                'labels' => SchoolOperatingProfile::labelsFor(SchoolOperatingProfile::DEFAULT_PRESET),
            ]);
            $this->grantSchoolMembership->grant($founder, $school, primary: false);

            return $school;
        });
    }
}
