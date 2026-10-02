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
        Schema::table('timetables', function (Blueprint $table): void {
            $table->foreignId('template_timetable_id')->nullable()->after('academic_cycle_section_id')->constrained('timetables')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropForeign(['template_timetable_id']);
            $table->dropColumn('template_timetable_id');
        });
    }
};
