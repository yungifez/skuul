<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollment_placements', function (Blueprint $table): void {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['my_class_id']);
            $table->dropForeign(['section_id']);
            $table->dropIndex('enrollment_placements_academic_year_id_my_class_id_index');
            $table->dropColumn(['my_class_id', 'section_id']);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });

        Schema::table('student_records', function (Blueprint $table): void {
            $table->dropForeign(['my_class_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['my_class_id', 'section_id']);
        });

        Schema::table('academic_year_student_record', function (Blueprint $table): void {
            $table->dropForeign(['my_class_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['my_class_id', 'section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('The legacy class and section placement columns cannot be restored.');
    }
};
