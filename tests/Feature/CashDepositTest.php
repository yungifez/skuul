<?php

namespace Tests\Feature;

use App\Actions\Finance\PostLedgerTransaction;
use App\Livewire\RecordCashDepositForm;
use App\Models\CashDeposit;
use App\Models\FinancialPeriod;
use App\Models\School;
use App\Services\Finance\ChartOfAccounts;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cash taken from the cash box to the bank.
 */
class CashDepositTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_person_without_permission_cannot_see_or_record_deposits(): void
    {
        $this->unauthorized_user();

        $this->get(route('cash-deposits.index'))->assertForbidden();
        $this->get(route('cash-deposits.create'))->assertForbidden();
        Livewire::test(RecordCashDepositForm::class)->assertForbidden();
    }

    public function test_a_bursar_moves_cash_to_the_bank(): void
    {
        $this->authorized_user(['read cash deposit', 'create cash deposit']);
        $this->putCashInTheBox(5000);

        $this->get(route('cash-deposits.create'))->assertOk()->assertSeeLivewire(RecordCashDepositForm::class);

        Livewire::test(RecordCashDepositForm::class)
            ->assertSee(money_text(5000))
            ->set('amount', '3000')
            ->assertSee(money_text(2000))
            ->set('bankReference', ' SLIP-104 ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('cash-deposits.index'));

        $deposit = CashDeposit::sole();
        $this->assertSame('SLIP-104', $deposit->bank_reference);
        $this->assertSame(2000.0, app(ChartOfAccounts::class)->account('cash')->balance());
        $this->assertSame(3000.0, app(ChartOfAccounts::class)->account('bank')->balance());

        $this->get(route('cash-deposits.index'))->assertOk()->assertSee('SLIP-104')->assertSee(money_text(3000));
    }

    public function test_a_deposit_above_the_cash_box_needs_a_second_yes(): void
    {
        $this->authorized_user(['create cash deposit']);
        $this->putCashInTheBox(500);

        $form = Livewire::test(RecordCashDepositForm::class)
            ->set('amount', '5000')
            ->call('save')
            ->assertHasErrors('amount')
            ->assertSet('isAboveCashBox', true)
            ->assertSee('The cash was counted and this amount is right');

        $this->assertSame(0, CashDeposit::query()->count());

        $form->set('confirmsMoreThanCashBox', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('cash-deposits.index'));

        $this->assertSame(1, CashDeposit::query()->count());
    }

    public function test_changing_the_amount_asks_for_the_second_yes_again(): void
    {
        $this->authorized_user(['create cash deposit']);

        Livewire::test(RecordCashDepositForm::class)
            ->set('amount', '5000')
            ->call('save')
            ->set('confirmsMoreThanCashBox', true)
            ->set('amount', '50000')
            ->assertSet('confirmsMoreThanCashBox', false)
            ->call('save')
            ->assertHasErrors('amount');

        $this->assertSame(0, CashDeposit::query()->count());
    }

    public function test_pressing_record_twice_records_one_deposit(): void
    {
        $this->authorized_user(['create cash deposit']);
        $this->putCashInTheBox(5000);

        $form = Livewire::test(RecordCashDepositForm::class)->set('amount', '1000');
        $form->call('save');
        $form->call('save');

        $this->assertSame(1, CashDeposit::query()->count());
    }

    public function test_bad_amounts_and_dates_are_refused(): void
    {
        $this->authorized_user(['create cash deposit']);
        $this->putCashInTheBox(5000);

        Livewire::test(RecordCashDepositForm::class)
            ->set('amount', '0')
            ->call('save')
            ->assertHasErrors(['amount' => 'gt'])
            ->set('amount', '10.555')
            ->call('save')
            ->assertHasErrors(['amount' => 'decimal'])
            ->set('amount', '100')
            ->set('depositDate', now()->addDay()->toDateString())
            ->call('save')
            ->assertHasErrors('depositDate')
            ->assertSee('A deposit cannot be dated in the future.');

        $this->assertSame(0, CashDeposit::query()->count());
    }

    public function test_a_deposit_outside_every_open_period_is_refused(): void
    {
        $this->authorized_user(['create cash deposit']);

        Livewire::test(RecordCashDepositForm::class)
            ->set('amount', '100')
            ->set('confirmsMoreThanCashBox', true)
            ->set('depositDate', now()->subYears(3)->toDateString())
            ->call('save')
            ->assertHasErrors('depositDate')
            ->assertNoRedirect();

        $this->assertSame(0, CashDeposit::query()->count());
    }

    public function test_the_list_shows_only_this_schools_deposits(): void
    {
        $otherSchool = School::factory()->create();
        FinancialPeriod::query()->create([
            'school_id' => $otherSchool->id,
            'name' => 'Other finance period',
            'starts_on' => now()->startOfYear()->toDateString(),
            'ends_on' => now()->endOfYear()->toDateString(),
        ]);
        school_context()->set($otherSchool, remember: false);
        CashDeposit::query()->create([
            'school_id' => $otherSchool->id,
            'financial_period_id' => FinancialPeriod::query()->where('school_id', $otherSchool->id)->value('id'),
            'amount' => 777,
            'deposit_date' => now(),
            'bank_reference' => 'OTHER-SCHOOL-SLIP',
        ]);
        school_context()->set($this->workingSchool(), remember: false);

        $this->authorized_user(['read cash deposit']);

        $this->get(route('cash-deposits.index'))->assertOk()->assertDontSee('OTHER-SCHOOL-SLIP');
    }

    /**
     * Record cash received into the working school's cash box.
     */
    private function putCashInTheBox(float $amount): void
    {
        $chart = app(ChartOfAccounts::class);

        app(PostLedgerTransaction::class)->post('Opening cash', [
            ['account' => $chart->account('cash'), 'debit' => $amount],
            ['account' => $chart->account('opening_balance'), 'credit' => $amount],
        ]);
    }
}
