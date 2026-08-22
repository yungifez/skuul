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
        Schema::table('calendar_event_audiences', function (Blueprint $table): void {
            $table->dropForeign(['my_class_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['my_class_id', 'section_id']);
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
