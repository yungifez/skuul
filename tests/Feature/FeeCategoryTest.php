<?php

namespace Tests\Feature;

use App\Models\FeeCategory;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeeCategoryTest extends TestCase
{
    use FeatureTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_view_all_fee_categories()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/fee-categories')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_all_fee_categories()
    {
        $this->authorized_user(['read fee category'])
            ->get('dashboard/fees/fee-categories')
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_view_create_fee_category()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/fee-categories/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_create_fee_category()
    {
        $this->authorized_user(['create fee category'])
            ->get('dashboard/fees/fee-categories/create')
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_store_a_fee_categories()
    {
        $name = $this->faker->name();
        $description = $this->faker->sentence();
        $this->unauthorized_user()
            ->post('dashboard/fees/fee-categories/', [
                'name'         => $name,
                'descripttion' => $description,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_categories', [
            'name'        => $name,
            'description' => $description,
        ]);
    }

    public function test_unauthorized_user_can_store_a_fee_categories()
    {
        $name = $this->faker->name();
        $description = $this->faker->sentence();
        $this->authorized_user(['create fee category'])
            ->post('dashboard/fees/fee-categories/', [
                'name'        => $name,
                'description' => $description,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_categories', [
            'name'        => $name,
            'description' => $description,
        ]);
    }

    public function test_unauthorized_user_cannot_view_edit_fee_category_page()
    {
        $FeeCategory = FeeCategory::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-categories/$FeeCategory->id/edit")
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_fee_category_page()
    {
        $FeeCategory = FeeCategory::factory()->create();

        $this->authorized_user(['update fee category'])
            ->get("dashboard/fees/fee-categories/$FeeCategory->id/edit")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_update_fee_category_page()
    {
        $FeeCategory = FeeCategory::factory()->create();
        $name = $this->faker()->name();
        $description = $this->faker()->sentence();

        $this->unauthorized_user()
            ->put("dashboard/fees/fee-categories/$FeeCategory->id", [
                'name'         => $name,
                'description'  => $description,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_categories', [
            'id'   => $FeeCategory->id,
            'name' => $name,
        ]);
    }

    public function test_authorized_user_can_update_fee_category_page()
    {
        $FeeCategory = FeeCategory::factory()->create();
        $name = $this->faker()->name();
        $description = $this->faker()->sentence();

        $this->authorized_user(['update fee category'])
            ->put("dashboard/fees/fee-categories/$FeeCategory->id", [
                'name'         => $name,
                'description'  => $description,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_categories', [
            'id'   => $FeeCategory->id,
            'name' => $name,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_fee_Category()
    {
        $FeeCategory = FeeCategory::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/fee-categories/$FeeCategory->id")
            ->assertForbidden();

        $this->assertModelExists($FeeCategory);
    }

    public function test_authorized_user_can_delete_fee_Category()
    {
        $FeeCategory = FeeCategory::factory()->create();

        $this->authorized_user(['delete fee category'])
            ->delete("dashboard/fees/fee-categories/$FeeCategory->id")
            ->assertRedirect();

        $this->assertSoftDeleted($FeeCategory);
    }
}
