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
        Schema::table('syllabi', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')
                ->after('file')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->dropForeign(['subject_id']);
            $table->dropForeign(['academic_period_id']);
            $table->dropColumn(['subject_id', 'academic_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Syllabi now belong to course offerings and cannot be restored to separate subject and period columns.');
    }
};
