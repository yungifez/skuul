<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropForeign(['academic_cycle_section_id']);
        });

        Schema::table('timetables', function (Blueprint $table): void {
            $table->foreignId('academic_cycle_section_id')->nullable()->change();
            $table->foreign('academic_cycle_section_id')->references('id')->on('academic_cycle_sections')->nullOnDelete();
        });

        Schema::table('timetable_time_slot_weekday', function (Blueprint $table): void {
            $table->string('audience_role', 30)->nullable()->after('timetable_time_slot_weekdayable_type');
        });
    }

    public function down(): void
    {
        Schema::table('timetable_time_slot_weekday', function (Blueprint $table): void {
            $table->dropColumn('audience_role');
        });

        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropForeign(['academic_cycle_section_id']);
        });

        Schema::table('timetables', function (Blueprint $table): void {
            $table->foreignId('academic_cycle_section_id')->nullable(false)->change();
            $table->foreign('academic_cycle_section_id')->references('id')->on('academic_cycle_sections')->cascadeOnDelete();
        });
    }
};
