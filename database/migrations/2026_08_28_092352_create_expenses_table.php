<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('financial_period_id')->constrained()->restrictOnDelete();
            $table->foreignId('ledger_account_id')->constrained('ledger_accounts')->restrictOnDelete();
            $table->foreignId('ledger_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('description', 255);
            $table->string('vendor', 150)->nullable();
            $table->string('method', 30);
            $table->string('reference', 100)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fund', 60)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'financial_period_id', 'expense_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
