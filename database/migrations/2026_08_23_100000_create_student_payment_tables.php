<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Money received and money applied are two different facts. A payment
     * says what arrived; an allocation says which invoice line it settled.
     * Keeping them apart is what lets one payment cover several invoices and
     * lets the rest sit as a credit until the next invoice needs it.
     */
    public function up(): void
    {
        Schema::create('student_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();

            // Minor units, like every other money column on the fee tables.
            // A reversal carries the negative of what it undoes.
            $table->bigInteger('amount');
            $table->string('method', 30);
            $table->string('reference', 100)->nullable();
            $table->date('received_on');
            $table->text('note')->nullable();
            $table->foreignId('ledger_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reversal_of_id')->nullable()->constrained('student_payments')->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['school_id', 'received_on']);
            $table->index(['student_record_id']);
        });

        Schema::create('payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_payment_id')->constrained()->cascadeOnDelete();

            // The invoice is kept beside the line so a statement never joins
            // twice to answer what one invoice has been paid.
            $table->foreignId('fee_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_invoice_record_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('amount');
            $table->foreignId('reversal_of_id')->nullable()->constrained('payment_allocations')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['fee_invoice_id']);
            $table->index(['fee_invoice_record_id']);
        });

        $this->carryForwardWhatWasAlreadyPaid();

        Schema::table('fee_invoice_records', function (Blueprint $table): void {
            $table->dropColumn('paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_invoice_records', function (Blueprint $table): void {
            $table->integer('paid')->default(0);
        });

        DB::table('payment_allocations')->orderBy('id')->chunkById(200, function ($allocations): void {
            foreach ($allocations as $allocation) {
                DB::table('fee_invoice_records')
                    ->where('id', $allocation->fee_invoice_record_id)
                    ->increment('paid', $allocation->amount);
            }
        });

        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('student_payments');
    }

    /**
     * Turn each old `paid` total into a payment and its allocation.
     *
     * These payments were taken before the books existed, so they name no
     * ledger entry. The amount still has to survive, because a family that
     * paid last term must not be asked for the money again.
     */
    private function carryForwardWhatWasAlreadyPaid(): void
    {
        $settled = DB::table('fee_invoice_records')
            ->join('fee_invoices', 'fee_invoices.id', '=', 'fee_invoice_records.fee_invoice_id')
            ->join('student_records', 'student_records.user_id', '=', 'fee_invoices.user_id')
            ->where('fee_invoice_records.paid', '>', 0)
            ->select([
                'fee_invoice_records.id as record_id',
                'fee_invoice_records.paid as paid',
                'fee_invoices.id as invoice_id',
                'fee_invoices.issue_date as issue_date',
                'student_records.id as student_record_id',
                'student_records.school_id as school_id',
            ])
            ->get();

        foreach ($settled as $record) {
            $paymentId = DB::table('student_payments')->insertGetId([
                'school_id' => $record->school_id,
                'student_record_id' => $record->student_record_id,
                'amount' => $record->paid,
                'method' => 'other',
                'reference' => null,
                'received_on' => $record->issue_date ?? now()->toDateString(),
                'note' => 'Recorded before the school kept payment records.',
                'created_at' => now(),
            ]);

            DB::table('payment_allocations')->insert([
                'student_payment_id' => $paymentId,
                'fee_invoice_id' => $record->invoice_id,
                'fee_invoice_record_id' => $record->record_id,
                'amount' => $record->paid,
                'created_at' => now(),
            ]);
        }
    }
};
