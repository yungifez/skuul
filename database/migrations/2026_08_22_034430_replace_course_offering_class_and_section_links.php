<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Replace obsolete course-offering class and section references.
     */
    public function up(): void
    {
        Schema::dropIfExists('course_offering_sections');

        if (Schema::hasForeignKey('course_offerings', ['my_class_id'])) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->dropForeign(['my_class_id']);
            });
        }

        if (Schema::hasForeignKey('course_offerings', ['academic_year_id'])) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->dropForeign(['academic_year_id']);
            });
        }

        if (Schema::hasIndex('course_offerings', ['academic_year_id', 'my_class_id'])) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->dropIndex(['academic_year_id', 'my_class_id']);
            });
        }

        if (Schema::hasColumn('course_offerings', 'my_class_id')) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->dropColumn('my_class_id');
            });
        }

        if (!Schema::hasColumn('course_offerings', 'academic_level_id')) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->foreignId('academic_level_id')
                    ->after('subject_id')
                    ->constrained()
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasIndex('course_offerings', ['academic_year_id', 'academic_level_id'])) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->index(['academic_year_id', 'academic_level_id']);
            });
        }

        if (!Schema::hasForeignKey('course_offerings', ['academic_year_id'])) {
            Schema::table('course_offerings', function (Blueprint $table): void {
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('course_offering_cycle_sections')) {
            Schema::create('course_offering_cycle_sections', function (Blueprint $table): void {
                $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
                $table->foreignId('academic_cycle_section_id')->constrained()->cascadeOnDelete();

                $table->primary(['course_offering_id', 'academic_cycle_section_id']);
                $table->index('academic_cycle_section_id');
            });
        }
    }

    /**
     * The deleted offering data cannot be restored.
     */
    public function down(): void
    {
        throw new LogicException('This destructive migration cannot be rolled back.');
    }
};
