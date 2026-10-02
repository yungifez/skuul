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
        Schema::table('grade_items', function (Blueprint $table) {
            $table->foreignId('grading_scale_id')->nullable()->after('type')->constrained()->nullOnDelete();
        });

        Schema::table('grade_entries', function (Blueprint $table) {
            $table->foreignId('grading_scale_option_id')->nullable()->after('points')->constrained()->nullOnDelete();
            $table->dropColumn('scale_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Free-text scale grades cannot be restored.');
    }
};
