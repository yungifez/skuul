<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Keep the exact current cycle section alongside the legacy class and
     * section pointers. Existing enrollments are deliberately not backfilled.
     */
    public function up(): void
    {
        Schema::table('student_records', function (Blueprint $table): void {
            $table->foreignId('academic_cycle_section_id')
                ->nullable()
                ->after('section_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_records', function (Blueprint $table): void {
            $table->dropForeign(['academic_cycle_section_id']);
            $table->dropColumn('academic_cycle_section_id');
        });
    }
};
