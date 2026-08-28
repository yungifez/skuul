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
        Schema::table('report_runs', function (Blueprint $table): void {
            $table->foreignId('financial_period_id')->nullable()->after('academic_period_id')->constrained()->nullOnDelete();
            $table->index(['school_id', 'financial_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_runs', function (Blueprint $table): void {
            $table->dropIndex(['school_id', 'financial_period_id']);
            $table->dropConstrainedForeignId('financial_period_id');
        });
    }
};
