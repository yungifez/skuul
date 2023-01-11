<?php

namespace Tests\Feature;

use App\Models\AccountApplication;
use App\Traits\FeatureTestTrait;
use Tests\TestCase;

class AccountApplicationTest extends TestCase
{
    use FeatureTestTrait;

    // test that unauthorized users cannot view account applications

    public function test_unauthorized_users_cannot_view_account_applications()
    {
        $this->unauthorized_user()
            ->get('dashboard/account-applications')
            ->assertForbidden();
    }

    // test that authorized users can view account applications

    public function test_unauthorized_users_can_view_account_applications()
    {
        $this->authorized_user(['read applicant'])
            ->get('dashboard/account-applications')
            ->assertSuccessful();
    }

    //test unauthorized cannot view an applicant

    public function test_unauthorized_cannot_view_an_applicant()
    {
        $applicant = AccountApplication::factory()->create()->user;

        $this->unauthorized_user()
            ->get("dashboard/account-applications/$applicant->id")
            ->assertForbidden();
    }

    //test authorized can view an applicant

    public function test_authorized_can_view_an_applicant()
    {
        $applicant = AccountApplication::factory()->create()->user;

        $this->authorized_user(['read applicant'])
            ->get("dashboard/account-applications/$applicant->id")
            ->assertSuccessful();
    }
}
