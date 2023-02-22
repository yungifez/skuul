<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeeTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use FeatureTestTrait;

    public function test_unauthorized_user_cannot_view_all_fees()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_all_fees()
    {
        $this->authorized_user(['read fee'])
            ->get('dashboard/fees')
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_view_create_fee()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_create_fee()
    {
        $this->authorized_user(['create fee'])
            ->get('dashboard/fees/create')
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_store_fee()
    {
        $name = $this->faker->name();
        $description = $this->faker->sentence();
        $feeCategory = FeeCategory::factory()->create();

        $this->unauthorized_user()
            ->post('dashboard/fees', [
                'name'             => $name,
                'description'      => $description,
                'fee_category_id'  => $feeCategory->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fees', [
            'name'    => $name,
        ]);
    }

    public function test_authorized_user_can_store_fee()
    {
        $name = $this->faker->name();
        $description = $this->faker->sentence();
        $feeCategory = FeeCategory::factory()->create();

        $this->authorized_user(['create fee'])
            ->post('dashboard/fees', [
                'name'             => $name,
                'description'      => $description,
                'fee_category_id'  => $feeCategory->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fees', [
            'name'    => $name,
        ]);
    }

    public function test_unauthorized_user_cannot_view_edit_fee()
    {
        $fee = Fee::factory()->create();
        $this->unauthorized_user()
            ->get("dashboard/fees/$fee->id/edit")
            ->assertForbidden();
    }

    public function test_unauthorized_user_can_view_edit_fee()
    {
        $fee = Fee::factory()->create();
        $this->authorized_user(['update fee'])
            ->get("dashboard/fees/$fee->id/edit")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_update_fee()
    {
        $fee = Fee::factory()->create();
        $name = $this->faker->name();
        $description = $this->faker->sentence();

        $this->unauthorized_user()
            ->put("dashboard/fees/$fee->id", [
                'name'        => $name,
                'description' => $description,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fees', [
            'id'   => $fee->id,
            'name' => $name,
        ]);
    }

    public function test_authorized_user_can_update_fee()
    {
        $fee = Fee::factory()->create();
        $name = $this->faker->name();
        $description = $this->faker->sentence();

        $this->authorized_user(['update fee'])
            ->put("dashboard/fees/$fee->id", [
                'name'        => $name,
                'description' => $description,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fees', [
            'id'   => $fee->id,
            'name' => $name,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_fee_Category()
    {
        $fee = Fee::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/$fee->id")
            ->assertForbidden();

        $this->assertModelExists($fee);
    }

    public function test_authorized_user_can_delete_fee_Category()
    {
        $fee = Fee::factory()->create();

        $this->authorized_user(['delete fee'])
            ->delete("dashboard/fees/$fee->id")
            ->assertRedirect();

        $this->assertSoftDeleted($fee);
    }
}
