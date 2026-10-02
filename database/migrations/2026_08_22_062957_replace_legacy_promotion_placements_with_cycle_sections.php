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
        Schema::table('promotions', function (Blueprint $table): void {
            $table->dropForeign(['old_class_id']);
            $table->dropForeign(['new_class_id']);
            $table->dropForeign(['old_section_id']);
            $table->dropForeign(['new_section_id']);
            $table->dropColumn(['old_class_id', 'new_class_id', 'old_section_id', 'new_section_id']);
            $table->foreignId('source_academic_cycle_section_id')->constrained('academic_cycle_sections')->cascadeOnDelete();
            $table->foreignId('destination_academic_cycle_section_id')->constrained('academic_cycle_sections')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('The legacy promotion placement columns cannot be restored.');
    }
};
