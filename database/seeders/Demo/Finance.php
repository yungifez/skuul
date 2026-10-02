<?php

namespace Database\Seeders\Demo;

use App\Actions\Finance\ChangeFinancialPeriodStatus;
use App\Actions\Finance\ReceivePayment;
use App\Actions\Finance\RecordExpense;
use App\Enums\FinancialPeriodStatus;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\FinancialPeriod;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Fee\FeeCategoryService;
use App\Services\Fee\FeeInvoiceService;
use App\Services\Fee\FeeService;
use App\Services\Finance\ChartOfAccounts;
use Illuminate\Support\Carbon;

/**
 * Fill the finance office: fees, invoices in every state, payments the
 * office took, two expenses, and a closed and an open financial period.
 *
 * Every learner gets a fall invoice. A quarter of the families paid it by
 * bank transfer, a quarter paid at the card machine, a quarter paid part of
 * it, and the rest have not paid and are overdue. Grade 10A also has a field
 * trip invoice that is not due yet. Ethan Brooks paid part of his fees in
 * cash, so his family sees a balance and the office can take the rest.
 */
class Finance implements DemoPart
{
    /**
     * The fees the school charges, by category.
     *
     * @var array<string, array{description: string, fees: array<string, string>}>
     */
    private const FEES = [
        'Activities and events' => [
            'description' => 'Clubs, school events, and the yearbook.',
            'fees' => [
                'Activity fee' => 'Covers clubs, assemblies, and student events for the year.',
                'Yearbook' => 'One printed copy of the Riverside yearbook.',
                'Science museum field trip' => 'Bus and entry for the Grade 10 science museum visit.',
            ],
        ],
        'Course materials' => [
            'description' => 'Materials that a course uses up.',
            'fees' => [
                'Lab fee' => 'Lab supplies and safety equipment for science classes.',
            ],
        ],
        'Athletics' => [
            'description' => 'School sports teams.',
            'fees' => [
                'Athletics participation fee' => 'Coaching, officials, and travel for one sport season.',
            ],
        ],
    ];

    /** @var array<string, Fee> by name */
    private array $fees = [];

    private DemoSchool $demo;

    private Carbon $today;

    public function seed(DemoSchool $demo): void
    {
        $this->demo = $demo;
        $this->today = school_today($demo->campus);

        $this->createFinancialPeriods();
        $this->createFees();
        $this->raiseFallInvoices();
        $this->raiseFieldTripInvoices();
        $this->recordExpenses();
    }

    /**
     * One financial period for each school year: last year's books are
     * closed, this year's are open. A period runs from the first day of
     * school to the day before the next year starts, so the summer stays in
     * the year that just ended.
     */
    private function createFinancialPeriods(): void
    {
        $startsOn = $this->demo->academicYear->starts_on->copy()->startOfDay();
        $lastYearStartsOn = $startsOn->copy()->subYear();

        $lastYear = FinancialPeriod::create([
            'school_id' => $this->demo->campus->id,
            'name' => $this->yearName($lastYearStartsOn).' school year',
            'starts_on' => $lastYearStartsOn->toDateString(),
            'ends_on' => $startsOn->copy()->subDay()->toDateString(),
        ]);

        FinancialPeriod::create([
            'school_id' => $this->demo->campus->id,
            'name' => $this->yearName($startsOn).' school year',
            'starts_on' => $startsOn->toDateString(),
            'ends_on' => $startsOn->copy()->addYear()->subDay()->toDateString(),
        ]);

        app(ChangeFinancialPeriodStatus::class)->change(
            $lastYear,
            FinancialPeriodStatus::Closed,
            'Year-end books closed after the district audit.',
            $this->demo->accountant,
        );
    }

    /**
     * Name a school year by the two calendar years it spans, such as 2026-27.
     */
    private function yearName(Carbon $startsOn): string
    {
        return $startsOn->year.'-'.substr((string) ($startsOn->year + 1), -2);
    }

    private function createFees(): void
    {
        foreach (self::FEES as $categoryName => $category) {
            $feeCategory = app(FeeCategoryService::class)->storeFeeCategory([
                'name' => $categoryName,
                'description' => $category['description'],
                'school_id' => $this->demo->campus->id,
            ]);

            foreach ($category['fees'] as $feeName => $description) {
                $this->fees[$feeName] = app(FeeService::class)->storeFee([
                    'name' => $feeName,
                    'description' => $description,
                    'fee_category_id' => $feeCategory->id,
                ]);
            }
        }
    }

    /**
     * Bill every learner for the fall, then take the payments families made.
     */
    private function raiseFallInvoices(): void
    {
        $issuedOn = $this->notBeforeTheYear($this->today->copy()->subWeeks(6));
        $dueOn = $issuedOn->max($this->today->copy()->subWeeks(2));

        foreach ($this->demo->allStudents() as $index => $student) {
            $lines = [
                $this->line('Activity fee', 150),
                $this->line('Lab fee', 45),
                $this->line('Yearbook', 65),
            ];

            if ($index % 3 === 0) {
                $lines[] = $this->line('Athletics participation fee', 175);
            }

            $invoice = $this->invoice([$this->enrollment($student)], $lines, $issuedOn, $dueOn, 'Fall semester student fees')[0];

            $this->takeFallPayment($student, $invoice, $index, $issuedOn);
        }
    }

    /**
     * Record what one family paid on the fall invoice.
     *
     * The demo student's family paid part of it in cash at the front office.
     * The others follow a fixed pattern, so the list mixes every state.
     */
    private function takeFallPayment(User $student, FeeInvoice $invoice, int $index, Carbon $issuedOn): void
    {
        $owedInCents = $invoice->balance->getMinorAmount()->toInt();
        $receivedOn = $issuedOn->max($this->today->copy()->subDays(4 + $index % 9));
        $enrollment = $this->enrollment($student);

        if ($student->is($this->demo->demoStudent)) {
            $this->pay($enrollment, $invoice, 15_000, 'cash', null, 'Paid at the front office', $receivedOn);

            return;
        }

        match ($index % 4) {
            0 => $this->pay($enrollment, $invoice, $owedInCents, 'bank_transfer', 'ACH-'.(240_100 + $index), 'Fall fees paid in full', $receivedOn),
            1 => $this->pay($enrollment, $invoice, 10_000, 'card', 'AUTH-'.(581_200 + $index * 7), 'First installment', $receivedOn),
            3 => $this->pay($enrollment, $invoice, $owedInCents, 'card', 'AUTH-'.(581_200 + $index * 7), 'Fall fees paid in full', $receivedOn),
            default => null,
        };
    }

    /**
     * Bill section 10A for a field trip that is not due yet.
     */
    private function raiseFieldTripInvoices(): void
    {
        $issuedOn = $this->notBeforeTheYear($this->today->copy()->subDays(3));
        $enrollments = array_map(fn (User $student): StudentRecord => $this->enrollment($student), $this->demo->students['10A']);

        $this->invoice($enrollments, [$this->line('Science museum field trip', 35)], $issuedOn, $this->today->copy()->addWeeks(3), 'Grade 10 field trip to the science museum');
    }

    /**
     * Two bills the school paid this year, so the summary shows money going
     * out as well as coming in.
     */
    private function recordExpenses(): void
    {
        $account = app(ChartOfAccounts::class)->account('operating_expenses', $this->demo->campus);

        app(RecordExpense::class)->record(
            $account,
            1200.00,
            'Yearbook printing deposit',
            'bank_transfer',
            $this->notBeforeTheYear($this->today->copy()->subDays(20)),
            vendor: 'Pacific Northwest Printing',
            reference: 'INV-30817',
            actor: $this->demo->accountant,
        );

        app(RecordExpense::class)->record(
            $account,
            2350.50,
            'Uniforms for the fall sports teams',
            'card',
            $this->notBeforeTheYear($this->today->copy()->subDays(9)),
            vendor: 'Cascade Sports Supply',
            reference: 'PO-1147',
            actor: $this->demo->accountant,
        );
    }

    /**
     * Raise one invoice for each learner through the screen's own service.
     *
     * @param  list<StudentRecord>  $enrollments
     * @param  list<array{fee_id: int, amount: int, waiver: int, fine: int}>  $lines
     * @return list<FeeInvoice> in the order of the learners
     */
    private function invoice(array $enrollments, array $lines, Carbon $issuedOn, Carbon $dueOn, string $note): array
    {
        $enrollmentIds = array_map(fn (StudentRecord $enrollment): int => $enrollment->id, $enrollments);

        app(FeeInvoiceService::class)->storeFeeInvoice([
            'issue_date' => $issuedOn->toDateString(),
            'due_date' => $dueOn->toDateString(),
            'note' => $note,
            'student_records' => $enrollmentIds,
            'records' => $lines,
        ]);

        return array_map(
            fn (int $enrollmentId): FeeInvoice => FeeInvoice::query()->inSchool()->where('student_record_id', $enrollmentId)->latest('id')->firstOrFail(),
            $enrollmentIds,
        );
    }

    /**
     * One fee on an invoice, in whole dollars.
     *
     * @return array{fee_id: int, amount: int, waiver: int, fine: int}
     */
    private function line(string $fee, int $dollars): array
    {
        return ['fee_id' => $this->fees[$fee]->id, 'amount' => $dollars, 'waiver' => 0, 'fine' => 0];
    }

    /**
     * Take money for one invoice through the office's payment action.
     */
    private function pay(StudentRecord $enrollment, FeeInvoice $invoice, int $cents, string $method, ?string $reference, string $note, Carbon $receivedOn): void
    {
        app(ReceivePayment::class)->receive(
            $enrollment,
            $cents,
            $method,
            onlyInvoice: $invoice->id,
            reference: $reference,
            note: $note,
            receivedOn: $receivedOn,
            actor: $this->demo->accountant,
        );
    }

    private function enrollment(User $student): StudentRecord
    {
        return StudentRecord::query()->inSchool()->where('user_id', $student->id)->sole();
    }

    /**
     * Keep a date inside this school year, so it falls in the open period.
     */
    private function notBeforeTheYear(Carbon $day): Carbon
    {
        return $day->max($this->demo->academicYear->starts_on->copy()->startOfDay());
    }
}
