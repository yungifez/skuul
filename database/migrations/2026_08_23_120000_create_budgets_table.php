<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A budget is a plan for one account over one stretch of the year. It can
     * be narrowed to a programme or to a fund, so a school group can ask what
     * the science programme was allowed to spend and what it actually spent.
     */
    public function up(): void
    {
        // The books only knew which account a line belonged to. Comparing a
        // plan with what happened needs the same dimensions on both sides.
        Schema::table('ledger_lines', function (Blueprint $table): void {
            $table->foreignId('program_id')->nullable()->after('student_record_id')->constrained()->nullOnDelete();
            $table->string('fund', 60)->nullable()->after('program_id');

            $table->index(['program_id']);
            $table->index(['fund']);
        });

        Schema::create('budgets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ledger_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fund', 60)->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();

            // What the budget is about, hashed, so one plan cannot be written
            // twice for the same account and stretch of the year.
            $table->char('scope_hash', 64);
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'scope_hash']);
            $table->index(['school_id', 'academic_year_id'], 'budgets_cycle_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');

        Schema::table('ledger_lines', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('program_id');
            $table->dropIndex(['fund']);
            $table->dropColumn('fund');
        });
    }
};
