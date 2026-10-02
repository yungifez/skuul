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
        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->dropForeign(['my_class_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['my_class_id', 'section_id']);
            $table->foreignId('academic_cycle_section_id')->nullable()->constrained()->nullOnDelete();
            $table->index(['academic_cycle_section_id', 'attended_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('The legacy attendance class and section columns cannot be restored.');
    }
};
