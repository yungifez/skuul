<?php

namespace Tests\Feature;

use App\Enums\TimetableStatus;
use App\Livewire\CreateCustomTimetableItemForm;
use App\Livewire\EditCustomTimetableItemForm;
use App\Livewire\ListCustomTimetableItemsTable;
use App\Models\CustomTimetableItem;
use App\Models\School;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
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
        $this->unauthorized_user();

        Livewire::test(CreateCustomTimetableItemForm::class)->assertForbidden();
    }

    public function test_authorized_users_can_store_custom_items(): void
    {
        $this->authorized_user(['create custom timetable item']);

        Livewire::test(CreateCustomTimetableItemForm::class)
            ->set('name', ' Assembly ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('custom-timetable-items.index'));

        $this->assertDatabaseHas('custom_timetable_items', ['name' => 'Assembly', 'school_id' => $this->workingSchool()->id]);
    }

    public function test_a_school_names_each_item_once(): void
    {
        CustomTimetableItem::factory()->create(['name' => 'Break', 'school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['create custom timetable item']);

        Livewire::test(CreateCustomTimetableItemForm::class)
            ->set('name', 'BREAK')
            ->call('save')
            ->assertHasErrors('name');

        $this->assertSame(1, CustomTimetableItem::inSchool()->where('name', 'like', 'break')->count());
    }

    public function test_another_school_may_use_the_same_name(): void
    {
        CustomTimetableItem::factory()->create(['name' => 'Break', 'school_id' => School::factory()->create()->id]);
        $this->authorized_user(['create custom timetable item']);

        Livewire::test(CreateCustomTimetableItemForm::class)
            ->set('name', 'Break')
            ->call('save')
            ->assertHasNoErrors();
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
        $this->unauthorized_user();

        Livewire::test(EditCustomTimetableItemForm::class, ['customTimetableItem' => $customItem])->assertForbidden();
    }

    public function test_authorized_users_can_update_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['update custom timetable item']);

        Livewire::test(EditCustomTimetableItemForm::class, ['customTimetableItem' => $customItem])
            ->assertSet('name', $customItem->name)
            ->set('name', 'Long break')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('custom-timetable-items.index'));

        $this->assertSame('Long break', $customItem->fresh()->name);
    }

    public function test_an_item_on_a_published_timetable_is_not_deleted(): void
    {
        $customItem = CustomTimetableItem::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->placeOn($customItem, TimetableStatus::Published);
        $this->authorized_user(['delete custom timetable item']);

        Livewire::test(ListCustomTimetableItemsTable::class)
            ->call('deleteItem', $customItem->id)
            ->assertDispatched('status-message', type: 'danger');

        $this->assertModelExists($customItem);
        $this->assertSame(1, $this->cellsHolding($customItem));
    }

    public function test_deleting_an_item_takes_it_off_draft_timetables(): void
    {
        $customItem = CustomTimetableItem::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->placeOn($customItem, TimetableStatus::Draft);
        $this->placeOn($customItem, TimetableStatus::Draft);
        $this->assertSame(2, $this->cellsHolding($customItem));
        $this->authorized_user(['delete custom timetable item']);

        Livewire::test(ListCustomTimetableItemsTable::class)
            ->call('deleteItem', $customItem->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertModelMissing($customItem);
        $this->assertSame(0, $this->cellsHolding($customItem));
    }

    public function test_unauthorized_users_cannot_delete_custom_items(): void
    {
        $customItem = CustomTimetableItem::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read custom timetable item']);

        Livewire::test(ListCustomTimetableItemsTable::class)
            ->call('deleteItem', $customItem->id)
            ->assertForbidden();

        $this->assertModelExists($customItem);
    }

    public function test_another_schools_item_cannot_be_deleted(): void
    {
        $theirs = CustomTimetableItem::factory()->create(['school_id' => School::factory()->create()->id]);
        $this->authorized_user(['read custom timetable item', 'delete custom timetable item']);

        try {
            Livewire::test(ListCustomTimetableItemsTable::class)->call('deleteItem', $theirs->id);
            $this->fail('Another school\'s item was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($theirs);
    }

    public function test_the_table_deletes_through_livewire_not_a_form(): void
    {
        $customItem = CustomTimetableItem::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read custom timetable item', 'delete custom timetable item']);

        Livewire::test(ListCustomTimetableItemsTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteItem&quot;, row.id)')
            ->assertDontSeeHtml('name="_method"');

        $this->assertFalse(Route::has('custom-timetable-items.destroy'));
        $this->delete("/dashboard/custom-timetable-items/{$customItem->id}")->assertStatus(405);
        $this->assertModelExists($customItem);
    }

    /**
     * Put an item in one cell of a new timetable.
     */
    private function placeOn(CustomTimetableItem $item, TimetableStatus $status): void
    {
        $timetable = Timetable::factory()->create(['status' => TimetableStatus::Draft]);
        $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '10:00', 'stop_time' => '10:30']);
        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::firstOrFail()->id,
            'timetable_time_slot_weekdayable_id' => $item->id,
            'timetable_time_slot_weekdayable_type' => $item->getMorphClass(),
        ]);
        Timetable::query()->whereKey($timetable->id)->update(['status' => $status->value]);
    }

    /**
     * Count the timetable cells that hold an item.
     */
    private function cellsHolding(CustomTimetableItem $item): int
    {
        return TimetableRecord::query()
            ->where('timetable_time_slot_weekdayable_type', $item->getMorphClass())
            ->where('timetable_time_slot_weekdayable_id', $item->id)
            ->count();
    }
}
