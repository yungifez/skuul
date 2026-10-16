<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status', 20)->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('close_reason')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'name']);
            $table->index(['school_id', 'status', 'starts_on', 'ends_on']);
        });

        Schema::table('fee_invoices', function (Blueprint $table): void {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('student_record_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('financial_period_id')->nullable()->after('student_record_id')->constrained()->nullOnDelete();
            $table->foreignId('ledger_transaction_id')->nullable()->after('financial_period_id')->constrained()->nullOnDelete();

            $table->index(['school_id', 'financial_period_id', 'due_date']);
            $table->index(['student_record_id', 'due_date']);
        });

        Schema::table('ledger_transactions', function (Blueprint $table): void {
            $table->foreignId('financial_period_id')->nullable()->after('school_id')->constrained()->nullOnDelete();
            $table->index(['school_id', 'financial_period_id', 'transaction_date'], 'ledger_period_date_index');
        });

        Schema::table('student_payments', function (Blueprint $table): void {
            $table->foreignId('financial_period_id')->nullable()->after('student_record_id')->constrained()->nullOnDelete();
            $table->index(['school_id', 'financial_period_id', 'received_on'], 'payments_period_date_index');
        });

        $this->createPeriodsFromAcademicYears();
        $this->backfillInvoices();
        $this->backfillPayments();
        $this->backfillTransactions();

        Schema::table('fee_invoices', function (Blueprint $table): void {
            $table->unique(['school_id', 'name'], 'fee_invoices_school_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table): void {
            $table->dropIndex('payments_period_date_index');
            $table->dropConstrainedForeignId('financial_period_id');
        });

        Schema::table('ledger_transactions', function (Blueprint $table): void {
            $table->dropIndex('ledger_period_date_index');
            $table->dropConstrainedForeignId('financial_period_id');
        });

        Schema::table('fee_invoices', function (Blueprint $table): void {
            $table->dropUnique('fee_invoices_school_name_unique');
            $table->dropIndex(['school_id', 'financial_period_id', 'due_date']);
            $table->dropIndex(['student_record_id', 'due_date']);
            $table->dropConstrainedForeignId('ledger_transaction_id');
            $table->dropConstrainedForeignId('financial_period_id');
            $table->dropConstrainedForeignId('student_record_id');
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::dropIfExists('financial_periods');
    }

    private function createPeriodsFromAcademicYears(): void
    {
        DB::table('academic_years')->orderBy('id')->get()->each(function (object $year): void {
            $startsOn = $year->starts_on ?? $year->start_year.'-01-01';
            $endsOn = $year->ends_on ?? $year->stop_year.'-12-31';

            DB::table('financial_periods')->insertOrIgnore([
                'school_id' => $year->school_id,
                'name' => $year->start_year.' - '.$year->stop_year,
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    private function backfillInvoices(): void
    {
        DB::table('fee_invoices')->orderBy('id')->get()->each(function (object $invoice): void {
            $schoolId = DB::table('fee_invoice_records')
                ->join('fees', 'fees.id', '=', 'fee_invoice_records.fee_id')
                ->join('fee_categories', 'fee_categories.id', '=', 'fees.fee_category_id')
                ->where('fee_invoice_records.fee_invoice_id', $invoice->id)
                ->value('fee_categories.school_id');

            $student = DB::table('student_records')
                ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId))
                ->where('user_id', $invoice->user_id)
                ->orderByDesc('is_primary')
                ->orderByDesc('id')
                ->first();

            $schoolId ??= $student?->school_id;

            if ($schoolId === null || $student === null) {
                return;
            }

            DB::table('fee_invoices')
                ->where('id', $invoice->id)
                ->update([
                    'school_id' => $schoolId,
                    'student_record_id' => $student->id,
                    'financial_period_id' => $this->periodFor($schoolId, $invoice->issue_date),
                ]);
        });
    }

    private function backfillPayments(): void
    {
        DB::table('student_payments')->orderBy('id')->get()->each(function (object $payment): void {
            DB::table('student_payments')
                ->where('id', $payment->id)
                ->update(['financial_period_id' => $this->periodFor($payment->school_id, $payment->received_on)]);
        });
    }

    private function backfillTransactions(): void
    {
        DB::table('ledger_transactions')->orderBy('id')->get()->each(function (object $transaction): void {
            $periodId = null;

            if ($transaction->source_type === 'App\\Models\\FeeInvoice' && $transaction->source_id !== null) {
                $periodId = DB::table('fee_invoices')->where('id', $transaction->source_id)->value('financial_period_id');
            }

            DB::table('ledger_transactions')
                ->where('id', $transaction->id)
                ->update([
                    'financial_period_id' => $periodId ?? $this->periodFor($transaction->school_id, $transaction->transaction_date),
                ]);
        });
    }

    private function periodFor(int $schoolId, string $date): int
    {
        $period = DB::table('financial_periods')
            ->where('school_id', $schoolId)
            ->whereDate('starts_on', '<=', $date)
            ->whereDate('ends_on', '>=', $date)
            ->orderBy('starts_on')
            ->first();

        if ($period !== null) {
            return $period->id;
        }

        $year = substr($date, 0, 4);
        $name = $year.' financial year';
        $existing = DB::table('financial_periods')
            ->where('school_id', $schoolId)
            ->where('name', $name)
            ->first();

        if ($existing !== null) {
            return $existing->id;
        }

        return DB::table('financial_periods')->insertGetId([
            'school_id' => $schoolId,
            'name' => $name,
            'starts_on' => $year.'-01-01',
            'ends_on' => $year.'-12-31',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
