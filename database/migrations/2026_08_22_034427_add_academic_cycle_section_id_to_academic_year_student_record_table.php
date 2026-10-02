<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep the current placement summary for a cycle without changing old
     * class and section summaries.
     */
    public function up(): void
    {
        Schema::table('academic_year_student_record', function (Blueprint $table): void {
            $table->foreignId('academic_cycle_section_id')
                ->nullable()
                ->after('section_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('academic_year_student_record', function (Blueprint $table): void {
            $table->dropForeign(['academic_cycle_section_id']);
            $table->dropColumn('academic_cycle_section_id');
        });
    }
};
