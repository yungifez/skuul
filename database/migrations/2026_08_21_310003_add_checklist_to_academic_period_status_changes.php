<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A close records what the readiness check found at that moment. Reading
     * the check again later gives a different answer, because the records it
     * counts keep moving.
     */
    public function up(): void
    {
        Schema::table('academic_period_status_changes', function (Blueprint $table): void {
            $table->json('checklist')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_period_status_changes', function (Blueprint $table): void {
            $table->dropColumn('checklist');
        });
    }
};
