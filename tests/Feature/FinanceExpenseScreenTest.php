<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\FinancialPeriod;
use App\Services\Finance\ChartOfAccounts;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceExpenseScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_office_can_record_an_expense_without_an_optional_programme(): void
    {
        $actor = $this->authorized_user(['create expense', 'read expense']);
        $school = $this->workingSchool();
        FinancialPeriod::create([
            'school_id' => $school->id,
            'name' => 'Term one',
            'starts_on' => now()->startOfYear()->toDateString(),
            'ends_on' => now()->endOfYear()->toDateString(),
        ]);

        $response = $actor->post(route('expenses.store'), [
            'description' => 'Classroom materials',
            'amount' => 450,
            'expense_date' => now()->toDateString(),
            'ledger_account_id' => app(ChartOfAccounts::class)->account('operating_expenses')->id,
            'method' => 'cash',
            'vendor' => 'Learning House Supplies',
            'reference' => 'EXP-001',
        ]);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'school_id' => $school->id,
            'description' => 'Classroom materials',
            'amount' => 450,
            'vendor' => 'Learning House Supplies',
            'reference' => 'EXP-001',
        ]);
        $this->assertSame(1, Expense::query()->count());
    }
}
