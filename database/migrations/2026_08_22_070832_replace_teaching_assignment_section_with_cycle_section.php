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
        Schema::table('teaching_assignments', function (Blueprint $table): void {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
            $table->foreignId('academic_cycle_section_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->index('academic_cycle_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('This destructive migration cannot be rolled back.');
    }
};
