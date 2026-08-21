<?php

use App\Enums\AcademicPeriodStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An academic period says whether it still accepts writes. Existing
     * periods are open, because their records are in use today.
     */
    public function up(): void
    {
        foreach (['academic_years', 'semesters'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('status', 20)->default(AcademicPeriodStatus::Open->value)->after('school_id');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['academic_years', 'semesters'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
    }
};
