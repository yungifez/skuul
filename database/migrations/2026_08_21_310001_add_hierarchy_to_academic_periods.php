<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A period can now sit inside another one, so a term holds its midterm,
     * its exam window, and its breaks. The local label carries the name the
     * school actually uses, which the type alone cannot express.
     */
    public function up(): void
    {
        Schema::table('academic_periods', function (Blueprint $table): void {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('academic_year_id')
                ->constrained('academic_periods')
                ->cascadeOnDelete();

            $table->string('label', 100)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_periods', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'label']);
        });
    }
};
