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
        Schema::table('grade_items', function (Blueprint $table): void {
            $table->foreignId('exam_slot_id')->nullable()->after('grading_scale_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('exam_slot_id');
        });
    }
};
