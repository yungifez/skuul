<?php

namespace Tests\Feature;

use App\Livewire\ListFeesTable;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\FinancialPeriod;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class FeeTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

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
                'name' => $name,
                'description' => $description,
                'fee_category_id' => $feeCategory->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fees', [
            'name' => $name,
        ]);
    }

    public function test_authorized_user_can_store_fee()
    {
        $name = $this->faker->name();
        $description = $this->faker->sentence();
        $feeCategory = FeeCategory::factory()->create();

        $this->authorized_user(['create fee'])
            ->post('dashboard/fees', [
                'name' => $name,
                'description' => $description,
                'fee_category_id' => $feeCategory->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fees', [
            'name' => $name,
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
                'name' => $name,
                'description' => $description,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fees', [
            'id' => $fee->id,
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
                'name' => $name,
                'description' => $description,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'name' => $name,
        ]);
    }

    /**
     * The invoice screens read the fee name straight off the relation, which
     * is null once the fee is soft deleted.
     */
    public function test_a_fee_on_an_invoice_cannot_be_deleted()
    {
        $school = $this->workingSchool();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $this->memberOf($school, $enrollment->user);

        FinancialPeriod::query()->firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Term one'],
            [
                'starts_on' => now()->startOfYear()->toDateString(),
                'ends_on' => now()->endOfYear()->toDateString(),
            ],
        );

        $feeInvoice = FeeInvoice::factory()->for($enrollment->user)->create([
            'school_id' => $school->id,
            'student_record_id' => $enrollment->id,
            'financial_period_id' => FinancialPeriod::query()->where('school_id', $school->id)->value('id'),
        ]);
        $fee = Fee::factory()->create([
            'fee_category_id' => FeeCategory::factory()->create(['school_id' => $school->id])->id,
        ]);
        FeeInvoiceRecord::factory()->create([
            'fee_invoice_id' => $feeInvoice->id,
            'fee_id' => $fee->id,
            'amount' => 500,
            'waiver' => 0,
            'fine' => 0,
        ]);

        $office = $this->authorized_user(['delete fee', 'read fee invoice']);

        $office->delete("dashboard/fees/$fee->id")
            ->assertRedirect()
            ->assertSessionHas('danger');

        $this->assertNotSoftDeleted($fee);

        $office->get("dashboard/fees/fee-invoices/$feeInvoice->id")->assertSuccessful();

        Livewire::test(ListFeesTable::class)
            ->assertSee('row.fee_invoice_records_count === 0', false);
    }

    public function test_unauthorized_user_cannot_delete_fee_category()
    {
        $fee = Fee::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/$fee->id")
            ->assertForbidden();

        $this->assertModelExists($fee);
    }

    public function test_authorized_user_can_delete_fee_category()
    {
        $fee = Fee::factory()->create();

        $this->authorized_user(['delete fee'])
            ->delete("dashboard/fees/$fee->id")
            ->assertRedirect();

        $this->assertSoftDeleted($fee);
    }
}
