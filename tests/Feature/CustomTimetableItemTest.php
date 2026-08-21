<?php

namespace Tests\Feature;

use App\Models\CustomTimetableItem;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomTimetableItemTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_unauthorized_users_cannot_see_all_custom_items(): void
    {
        $response = $this->unauthorized_user()
            ->get('/dashboard/custom-timetable-items');

        $response->assertForbidden();
    }

    public function test_authorized_users_can_see_all_custom_items(): void
    {
        $response = $this->authorized_user(['read custom timetable item'])
            ->get('/dashboard/custom-timetable-items');

        $response->assertSuccessful();
    }

    public function test_unauthorized_users_cannot_see_create_custom_items(): void
    {
        $response = $this->unauthorized_user()
            ->get('/dashboard/custom-timetable-items/create');

        $response->assertForbidden();
    }

    public function test_authorized_users_can_see_create_custom_items(): void
    {
        $response = $this->authorized_user(['create custom timetable item'])
            ->get('/dashboard/custom-timetable-items/create');

        $response->assertSuccessful();
    }

    public function test_unauthorized_users_cannot_store_custom_items(): void
    {
        $name = $this->faker->name();
        $response = $this->unauthorized_user()
            ->post('/dashboard/custom-timetable-items', [
                'name' => $name,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('custom_timetable_items', [
            'name' => $name,
        ]);
    }

    public function test_authorized_users_can_store_custom_items(): void
    {
        $name = $this->faker->name();
        $response = $this->authorized_user(['create custom timetable item'])
            ->post('/dashboard/custom-timetable-items', [
                'name' => $name,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('custom_timetable_items', [
            'name' => $name,
        ]);
    }

    public function test_unauthorized_users_cannot_see_edit_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create();
        $response = $this->unauthorized_user()
            ->get("/dashboard/custom-timetable-items/$customItem->id/edit");

        $response->assertForbidden();
    }

    public function test_authorized_users_can_see_edit_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create();
        $response = $this->authorized_user(['update custom timetable item'])
            ->get("/dashboard/custom-timetable-items/$customItem->id/edit");

        $response->assertSuccessful();
    }

    public function test_unauthorized_users_cannot_update_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create();
        $name = $this->faker->name();
        $response = $this->unauthorized_user()
            ->put("/dashboard/custom-timetable-items/$customItem->id", [
                'name' => $name,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('custom_timetable_items', [
            'name' => $name,
            'id'   => $customItem->id,
        ]);
    }

    public function test_authorized_users_can_update_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create();
        $name = $this->faker->name();
        $response = $this->authorized_user(['update custom timetable item'])
            ->put("/dashboard/custom-timetable-items/$customItem->id", [
                'name' => $name,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('custom_timetable_items', [
            'name' => $name,
            'id'   => $customItem->id,
        ]);
    }

    public function test_unauthorized_users_cannot_delete_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create();
        $response = $this->unauthorized_user()
            ->delete("/dashboard/custom-timetable-items/$customItem->id");

        $response->assertForbidden();

        $this->assertModelExists($customItem);
    }

    public function test_authorized_users_can_delete_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create();
        $response = $this->authorized_user(['delete custom timetable item'])
            ->delete("/dashboard/custom-timetable-items/$customItem->id");

        $response->assertRedirect();

        $this->assertModelMissing($customItem);
    }
}
