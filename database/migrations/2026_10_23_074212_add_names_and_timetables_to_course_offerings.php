<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Let a school name an offering, such as Algebra B, and give an offering its own timetable.
 *
 * A campus that teaches a timetable of separate subjects can run two
 * offerings of one subject for one level. Each one meets at its own times,
 * which do not belong to any one section.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_offerings', function (Blueprint $table): void {
            $table->string('name', 100)->nullable()->after('subject_id');
        });

        Schema::table('timetables', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')
                ->nullable()
                ->after('academic_cycle_section_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('course_offering_id');
        });

        Schema::table('course_offerings', function (Blueprint $table): void {
            $table->dropColumn('name');
        });
    }
};
