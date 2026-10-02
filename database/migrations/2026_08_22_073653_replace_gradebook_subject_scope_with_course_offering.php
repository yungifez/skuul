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
        Schema::table('grade_categories', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable()->after('school_id')->constrained()->cascadeOnDelete();
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['academic_period_id']);
            $table->dropColumn(['subject_id', 'academic_year_id', 'academic_period_id']);
        });

        Schema::table('grade_items', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable()->after('school_id')->constrained()->cascadeOnDelete();
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['academic_period_id']);
            $table->dropColumn(['subject_id', 'academic_year_id', 'academic_period_id']);
        });

        Schema::table('result_snapshots', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable()->after('school_id')->constrained()->cascadeOnDelete();
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['academic_period_id']);
            $table->dropColumn(['subject_id', 'academic_year_id', 'academic_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Gradebook and result records now identify their course offering directly.');
    }
};
