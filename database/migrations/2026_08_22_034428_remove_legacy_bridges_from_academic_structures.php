<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Remove obsolete class and section mappings from the replacement model.
     */
    public function up(): void
    {
        Schema::table('academic_cycle_sections', function (Blueprint $table): void {
            $table->dropForeign(['legacy_section_id']);
            $table->dropIndex(['legacy_section_id']);
            $table->dropColumn('legacy_section_id');
        });

        Schema::table('academic_levels', function (Blueprint $table): void {
            $table->dropForeign(['legacy_my_class_id']);
            $table->dropUnique(['legacy_my_class_id']);
            $table->dropColumn('legacy_my_class_id');
        });
    }

    /**
     * Discarded bridge mappings cannot be restored.
     */
    public function down(): void
    {
        throw new LogicException('This destructive migration cannot be rolled back.');
    }
};
