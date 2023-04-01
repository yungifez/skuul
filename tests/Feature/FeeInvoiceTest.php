<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeeInvoiceTest extends TestCase
{
    use FeatureTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_view_all_fee_invoices()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/fee-invoices')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_all_fee_invoices()
    {
        $this->authorized_user(['read fee invoice'])
            ->get('dashboard/fees/fee-invoices')
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_view_create_fee_invoice()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/fee-invoices/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_create_fee_invoice()
    {
        $this->authorized_user(['create fee invoice'])
            ->get('dashboard/fees/fee-invoices/create')
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_create_fee_invoice()
    {
        $studentRecords = StudentRecord::factory()->count(10)->create();
        $fees = Fee::factory()->count(4)->create();
        $records = [];
        foreach ($fees as $fee) {
            $amount = mt_rand(100, 10000);
            $waiver = $amount - 10;
            $fine = $amount - 20;
            array_push($records, [
                'fee_id' => $fee->id,
                'amount' => $amount,
                'waiver' => $waiver,
                'fine'   => $fine,
            ]);
        }
        $date = now();
        $students = $studentRecords->map(function ($student) {
            return $student->user;
        });

        $this->unauthorized_user()
            ->post('dashboard/fees/fee-invoices', [
                'issue_date' => $date,
                'due_date'   => $date->addDay(),
                'note'       => $this->faker()->sentence(),
                'users'      => $students->pluck('id'),
                'records'    => $records,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoices', [
            'user_id'    => $students->first()->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);

        $this->assertDatabaseMissing('fee_invoices', [
            'user_id'    => $students[2]->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);
    }

    public function test_authorized_user_can_create_fee_invoice()
    {
        $studentRecords = StudentRecord::factory()->count(10)->create();
        $fees = Fee::factory()->count(4)->create();
        $records = [];
        foreach ($fees as $fee) {
            $amount = mt_rand(100, 10000);
            $waiver = $amount - 10;
            $fine = $amount - 20;
            array_push($records, [
                'fee_id' => $fee->id,
                'amount' => $amount,
                'waiver' => $waiver,
                'fine'   => $fine,
            ]);
        }
        $date = now();
        $students = $studentRecords->map(function ($student) {
            return $student->user;
        });

        $this->authorized_user(['create fee invoice'])
            ->post('dashboard/fees/fee-invoices', [
                'issue_date' => $date,
                'due_date'   => Carbon::instance($date)->addDay(),
                'note'       => $this->faker()->sentence(),
                'users'      => $students->pluck('id')->all(),
                'records'    => $records,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_invoices', [
            'user_id'    => $students->first()->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('fee_invoices', [
            'user_id'    => $students[2]->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);
    }

    public function test_unauthorized_user_cannot_view_show_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_show_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['read fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_print_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/print")
            ->assertForbidden();
    }

    public function test_authorized_user_can_print_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['read fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/print")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_view_edit_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/edit")
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['update fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/edit")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_update_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $issueDate = $this->faker->date();
        $dueDate = $this->faker->date();

        $this->unauthorized_user()
            ->put("dashboard/fees/fee-invoices/$feeInvoice->id/", [
                'issue_date' => $issueDate,
                'due_date'   => $dueDate,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoices', [
            'id'         => $feeInvoice->id,
            'issue_date' => $issueDate,
            'due_date'   => $dueDate,
        ]);
    }

    public function test_authorized_user_can_update_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $issueDate = $this->faker->date();
        $dueDate = Carbon::parse($issueDate)->addDays(10);

        $this->authorized_user(['update fee invoice'])
            ->put("dashboard/fees/fee-invoices/$feeInvoice->id/", [
                'issue_date' => $issueDate,
                'due_date'   => $dueDate,

            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_invoices', [
            'id'         => $feeInvoice->id,
            'issue_date' => $issueDate,
            'due_date'   => $dueDate,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertForbidden();

        $this->assertModelExists($feeInvoice);

        $this->assertNotSoftDeleted($feeInvoice);
    }

    public function test_authorized_user_can_delete_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['delete fee invoice'])
            ->delete("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertRedirect();

        $this->assertModelExists($feeInvoice);

        $this->assertSoftDeleted($feeInvoice);
    }
}
