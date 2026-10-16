<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep the exact cycle section on new placement history rows. Old rows
     * remain unchanged until a verified historic-data migration is approved.
     */
    public function up(): void
    {
        Schema::table('enrollment_placements', function (Blueprint $table): void {
            $table->foreignId('academic_cycle_section_id')
                ->nullable()
                ->after('section_id')
                ->constrained()
                ->nullOnDelete();

            $table->index(
                ['student_record_id', 'academic_year_id', 'academic_cycle_section_id'],
                'enrollment_placements_cycle_section_lookup_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_placements', function (Blueprint $table): void {
            $table->dropIndex('enrollment_placements_cycle_section_lookup_index');
            $table->dropForeign(['academic_cycle_section_id']);
            $table->dropColumn('academic_cycle_section_id');
        });
    }
};
