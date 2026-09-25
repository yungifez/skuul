<?php

namespace Tests\Feature;

use App\Livewire\CreateFeeCategoryForm;
use App\Livewire\EditFeeCategoryForm;
use App\Livewire\ListFeeCategoriesTable;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\School;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class FeeCategoryTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

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
                'name' => $name,
                'descripttion' => $description,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_categories', [
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function test_unauthorized_user_can_store_a_fee_categories()
    {
        $name = $this->faker->name();
        $description = $this->faker->sentence();
        $this->authorized_user(['create fee category'])
            ->post('dashboard/fees/fee-categories/', [
                'name' => $name,
                'description' => $description,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_categories', [
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function test_fee_category_create_livewire_flow_saves_in_the_working_school(): void
    {
        $this->authorized_user(['create fee category']);

        Livewire::test(CreateFeeCategoryForm::class)
            ->set('name', 'Boarding fees')
            ->set('description', 'Charges for residential students.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('fee-categories.index'));

        $this->assertDatabaseHas('fee_categories', [
            'name' => 'Boarding fees',
            'description' => 'Charges for residential students.',
            'school_id' => $this->workingSchool()->id,
        ]);
    }

    public function test_fee_category_create_livewire_flow_reports_invalid_input(): void
    {
        $this->authorized_user(['create fee category']);

        Livewire::test(CreateFeeCategoryForm::class)
            ->set('name', '')
            ->set('description', str_repeat('x', 10001))
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'description' => 'max']);
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

    public function test_fee_category_edit_livewire_flow_updates_record(): void
    {
        $feeCategory = FeeCategory::factory()->create();
        $this->authorized_user(['update fee category']);

        Livewire::test(EditFeeCategoryForm::class, ['feeCategory' => $feeCategory])
            ->set('name', 'Updated fees')
            ->set('description', 'Updated description')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('fee-categories.index'));

        $this->assertSame('Updated fees', $feeCategory->fresh()->name);
        $this->assertSame('Updated description', $feeCategory->fresh()->description);
    }

    public function test_livewire_fee_category_edit_refuses_a_record_from_another_school(): void
    {
        $otherSchool = School::factory()->create();
        $feeCategory = FeeCategory::factory()->create(['school_id' => $otherSchool->id]);
        $this->authorized_user(['update fee category']);

        Livewire::test(EditFeeCategoryForm::class, ['feeCategory' => $feeCategory])
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_update_fee_category_page()
    {
        $FeeCategory = FeeCategory::factory()->create();
        $name = $this->faker()->name();
        $description = $this->faker()->sentence();

        $this->unauthorized_user()
            ->put("dashboard/fees/fee-categories/$FeeCategory->id", [
                'name' => $name,
                'description' => $description,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_categories', [
            'id' => $FeeCategory->id,
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
                'name' => $name,
                'description' => $description,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_categories', [
            'id' => $FeeCategory->id,
            'name' => $name,
        ]);
    }

    /**
     * The fees index and the fee policy both read the category off the
     * relation, which is null once the category is soft deleted.
     */
    public function test_a_category_that_holds_fees_cannot_be_deleted()
    {
        $school = $this->workingSchool();
        $feeCategory = FeeCategory::factory()->create(['school_id' => $school->id]);
        Fee::factory()->create(['fee_category_id' => $feeCategory->id]);

        $office = $this->authorized_user(['delete fee category', 'read fee']);

        $office->delete("dashboard/fees/fee-categories/$feeCategory->id")
            ->assertRedirect()
            ->assertSessionHas('danger');

        $this->assertNotSoftDeleted($feeCategory);

        $office->get('dashboard/fees')->assertSuccessful();

        Livewire::test(ListFeeCategoriesTable::class)
            ->assertSee('row.fees_count === 0', false);
    }

    public function test_unauthorized_user_cannot_delete_fee_category()
    {
        $FeeCategory = FeeCategory::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/fee-categories/$FeeCategory->id")
            ->assertForbidden();

        $this->assertModelExists($FeeCategory);
    }

    public function test_authorized_user_can_delete_fee_category()
    {
        $FeeCategory = FeeCategory::factory()->create();

        $this->authorized_user(['delete fee category'])
            ->delete("dashboard/fees/fee-categories/$FeeCategory->id")
            ->assertRedirect();

        $this->assertSoftDeleted($FeeCategory);
    }
}
