<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('teaching_assignments')->whereNull('course_offering_id')->delete();

        Schema::table('teaching_assignments', function (Blueprint $table): void {
            $table->dropForeign(['course_offering_id']);
        });

        Schema::table('teaching_assignments', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable(false)->change();
        });

        Schema::table('teaching_assignments', function (Blueprint $table): void {
            $table->foreign('course_offering_id')
                ->references('id')
                ->on('course_offerings')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('The retired subject-level teaching assignments cannot be restored.');
    }
};
