<?php

namespace Tests\Feature;

use App\Actions\Finance\ChargeStudent;
use App\Actions\Finance\PostLedgerTransaction;
use App\Actions\Finance\RecordStudentPayment;
use App\Actions\Finance\RelieveStudentFees;
use App\Actions\Finance\ReverseLedgerTransaction;
use App\Enums\AuditAction;
use App\Enums\LedgerAccountType;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\LedgerAccount;
use App\Models\LedgerTransaction;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\StudentLedger;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * School money is kept in balanced, unchangeable entries.
 */
class LedgerTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_school_starts_with_a_chart_of_accounts(): void
    {
        $accounts = app(ChartOfAccounts::class)->ensureFor($this->workingSchool());

        $this->assertNotNull($accounts['fees_receivable']);
        $this->assertSame(LedgerAccountType::Asset, $accounts['fees_receivable']->type);
        $this->assertSame(LedgerAccountType::Income, $accounts['tuition_income']->type);
        $this->assertSame(count(ChartOfAccounts::purposes()), $accounts->count());
    }

    public function test_the_chart_is_created_once(): void
    {
        $chart = app(ChartOfAccounts::class);
        $chart->ensureFor($this->workingSchool());
        $chart->ensureFor($this->workingSchool());

        $this->assertSame(
            count(ChartOfAccounts::purposes()),
            LedgerAccount::where('school_id', $this->workingSchool()->id)->count()
        );
    }

    public function test_an_entry_must_balance(): void
    {
        $this->authorized_user([]);
        $chart = app(ChartOfAccounts::class);

        $this->expectException(InvalidValueException::class);

        app(PostLedgerTransaction::class)->post('Wrong entry', [
            ['account' => $chart->account('cash'), 'debit' => 100],
            ['account' => $chart->account('tuition_income'), 'credit' => 90],
        ]);
    }

    public function test_a_line_cannot_be_a_debit_and_a_credit(): void
    {
        $this->authorized_user([]);
        $chart = app(ChartOfAccounts::class);

        $this->expectException(InvalidValueException::class);

        app(PostLedgerTransaction::class)->post('Wrong entry', [
            ['account' => $chart->account('cash'), 'debit' => 100, 'credit' => 100],
            ['account' => $chart->account('tuition_income'), 'credit' => 100],
        ]);
    }

    public function test_an_entry_cannot_cross_two_schools(): void
    {
        $this->authorized_user([]);
        $chart = app(ChartOfAccounts::class);
        $other = School::factory()->create();

        $this->expectException(InvalidValueException::class);

        app(PostLedgerTransaction::class)->post('Wrong entry', [
            ['account' => $chart->account('cash'), 'debit' => 100],
            ['account' => $chart->account('tuition_income', $other), 'credit' => 100],
        ]);
    }

    public function test_a_posted_entry_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $transaction = $this->charge(500);

        $this->expectException(RuntimeException::class);

        $transaction->update(['description' => 'Something else']);
    }

    public function test_charging_a_student_makes_them_owe_the_school(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();

        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        $this->assertSame(500.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame(500.0, app(ChartOfAccounts::class)->account('tuition_income')->balance());
    }

    public function test_a_payment_pays_down_what_is_owed(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(RecordStudentPayment::class)->record($enrollment, 200);

        $ledger = app(StudentLedger::class);

        $this->assertSame(300.0, $ledger->balance($enrollment));
        $this->assertSame(200.0, app(ChartOfAccounts::class)->account('cash')->balance());
    }

    public function test_money_paid_over_the_balance_is_held_as_credit(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(RecordStudentPayment::class)->record($enrollment, 800);

        $ledger = app(StudentLedger::class);

        $this->assertSame(0.0, $ledger->balance($enrollment));
        $this->assertSame(300.0, $ledger->unappliedCredit($enrollment));
    }

    public function test_a_waiver_lowers_the_balance_and_shows_as_a_cost(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(RelieveStudentFees::class)->waive($enrollment, 150, 'Staff child');

        $this->assertSame(350.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame(150.0, app(ChartOfAccounts::class)->account('scholarships')->balance());
    }

    public function test_a_write_off_is_kept_apart_from_a_waiver(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(RelieveStudentFees::class)->writeOff($enrollment, 500, 'The family cannot be reached');

        $chart = app(ChartOfAccounts::class);

        $this->assertSame(0.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame(500.0, $chart->account('bad_debt')->balance());
        $this->assertSame(0.0, $chart->account('scholarships')->balance());
    }

    public function test_a_reversal_cancels_the_entry_and_keeps_both(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $charge = app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        $reversal = app(ReverseLedgerTransaction::class)->reverse($charge, 'Charged the wrong student');

        $this->assertSame(0.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame($charge->id, $reversal->reversal_of_id);
        $this->assertTrue($charge->fresh()->isReversed());
    }

    public function test_an_entry_cannot_be_reversed_twice(): void
    {
        $this->authorized_user([]);
        $charge = $this->charge(500);
        app(ReverseLedgerTransaction::class)->reverse($charge, 'Wrong student');

        $this->expectException(InvalidValueException::class);

        app(ReverseLedgerTransaction::class)->reverse($charge->fresh(), 'Wrong again');
    }

    public function test_a_reversal_cannot_itself_be_reversed(): void
    {
        $this->authorized_user([]);
        $reversal = app(ReverseLedgerTransaction::class)->reverse($this->charge(500), 'Wrong student');

        $this->expectException(InvalidValueException::class);

        app(ReverseLedgerTransaction::class)->reverse($reversal, 'Changed my mind');
    }

    public function test_a_statement_lists_every_line_about_the_student(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');
        app(RecordStudentPayment::class)->record($enrollment, 200);

        $this->assertSame(4, app(StudentLedger::class)->statement($enrollment)->count());
    }

    public function test_posting_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $charge = $this->charge(500);

        $this->assertNotNull(
            AuditEvent::ofAction(AuditAction::LedgerTransactionPosted)->forSubject($charge)->first()
        );
    }

    /**
     * Charge a new student and return the entry.
     */
    private function charge(float $amount): LedgerTransaction
    {
        return app(ChargeStudent::class)->charge($this->enrollment(), $amount, 'Term one fees');
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }
}
