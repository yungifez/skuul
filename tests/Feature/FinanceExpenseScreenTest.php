<?php

namespace Tests\Feature;

use App\Actions\Finance\PostLedgerTransaction;
use App\Enums\ProgramType;
use App\Livewire\RecordExpenseForm;
use App\Models\Expense;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Models\School;
use App\Services\Finance\ChartOfAccounts;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceExpenseScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_person_without_permission_cannot_record_an_expense(): void
    {
        $this->unauthorized_user();

        $this->get(route('expenses.create'))->assertForbidden();
        Livewire::test(RecordExpenseForm::class)->assertForbidden();
    }

    public function test_the_office_can_record_an_expense_without_an_optional_programme(): void
    {
        $this->authorized_user(['create expense', 'read expense']);
        $this->fund('cash', 1000);

        $this->get(route('expenses.create'))->assertOk()->assertSeeLivewire(RecordExpenseForm::class);

        Livewire::test(RecordExpenseForm::class)
            ->assertSee(money_text(1000))
            ->set('description', ' Classroom materials ')
            ->set('amount', '450')
            ->assertSee(money_text(550))
            ->set('ledgerAccountId', (string) $this->expenseAccount()->id)
            ->set('vendor', 'Learning House Supplies')
            ->set('reference', 'EXP-001')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'school_id' => $this->workingSchool()->id,
            'description' => 'Classroom materials',
            'amount' => 450,
            'vendor' => 'Learning House Supplies',
            'reference' => 'EXP-001',
            'program_id' => null,
        ]);

        $this->get(route('expenses.index'))->assertOk()->assertSee('Classroom materials')->assertSee('Learning House Supplies');
    }

    public function test_a_bank_transfer_needs_its_reference(): void
    {
        $this->authorized_user(['create expense']);
        $this->fund('bank', 1000);

        Livewire::test(RecordExpenseForm::class)
            ->set('description', 'Internet')
            ->set('amount', '100')
            ->set('ledgerAccountId', (string) $this->expenseAccount()->id)
            ->set('method', 'bank_transfer')
            ->assertSee('Reference from the statement or slip')
            ->call('save')
            ->assertHasErrors(['reference' => 'required'])
            ->set('reference', 'TRF-889')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('bank_transfer', Expense::sole()->method);
    }

    public function test_an_expense_above_what_the_account_holds_needs_a_second_yes(): void
    {
        $this->authorized_user(['create expense']);
        $this->fund('cash', 100);

        $form = Livewire::test(RecordExpenseForm::class)
            ->set('description', 'Generator repair')
            ->set('amount', '4500')
            ->set('ledgerAccountId', (string) $this->expenseAccount()->id)
            ->call('save')
            ->assertHasErrors('amount')
            ->assertSee('This amount is right');

        $this->assertSame(0, Expense::query()->count());

        $form->set('method', 'bank_transfer')->assertSet('isAboveBalance', false);

        $form->set('method', 'cash')
            ->call('save')
            ->set('confirmsMoreThanBalance', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, Expense::query()->count());
    }

    public function test_pressing_record_twice_records_one_expense(): void
    {
        $this->authorized_user(['create expense']);
        $this->fund('cash', 1000);

        $form = Livewire::test(RecordExpenseForm::class)
            ->set('description', 'Chalk')
            ->set('amount', '20')
            ->set('ledgerAccountId', (string) $this->expenseAccount()->id);
        $form->call('save');
        $form->call('save');

        $this->assertSame(1, Expense::query()->count());
    }

    public function test_another_schools_account_and_programme_are_refused(): void
    {
        $this->authorized_user(['create expense']);
        $this->fund('cash', 1000);
        $otherSchool = School::factory()->create();
        $foreignAccount = app(ChartOfAccounts::class)->account('operating_expenses', $otherSchool);
        $foreignProgram = Program::create(['school_id' => $otherSchool->id, 'name' => 'Foreign boarding programme', 'type' => ProgramType::cases()[0]]);

        Livewire::test(RecordExpenseForm::class)
            ->assertDontSee($foreignProgram->name)
            ->set('description', 'Chalk')
            ->set('amount', '20')
            ->set('ledgerAccountId', (string) $foreignAccount->id)
            ->set('programId', (string) $foreignProgram->id)
            ->call('save')
            ->assertHasErrors(['ledgerAccountId', 'programId']);

        $this->assertSame(0, Expense::query()->count());
    }

    public function test_a_future_date_and_an_asset_account_are_refused(): void
    {
        $this->authorized_user(['create expense']);

        Livewire::test(RecordExpenseForm::class)
            ->set('description', 'Chalk')
            ->set('amount', '20')
            ->set('expenseDate', now()->addDay()->toDateString())
            ->set('ledgerAccountId', (string) app(ChartOfAccounts::class)->account('bank')->id)
            ->call('save')
            ->assertHasErrors(['expenseDate', 'ledgerAccountId'])
            ->assertSee('An expense cannot be dated in the future.');

        $this->assertSame(0, Expense::query()->count());
    }

    private function expenseAccount(): LedgerAccount
    {
        return app(ChartOfAccounts::class)->account('operating_expenses');
    }

    /**
     * Put money in one of the working school's accounts.
     */
    private function fund(string $purpose, float $amount): void
    {
        $chart = app(ChartOfAccounts::class);

        app(PostLedgerTransaction::class)->post('Opening money', [
            ['account' => $chart->account($purpose), 'debit' => $amount],
            ['account' => $chart->account('opening_balance'), 'credit' => $amount],
        ]);
    }
}
