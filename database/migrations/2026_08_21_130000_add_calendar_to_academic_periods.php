<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An academic year now holds ordered periods with a kind and dates, so a
     * school can use terms, semesters, or quarters as its calendar requires.
     */
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table): void {
            $table->date('starts_on')->nullable()->after('stop_year');
            $table->date('ends_on')->nullable()->after('starts_on');
        });

        Schema::table('semesters', function (Blueprint $table): void {
            $table->string('type', 20)->default('semester')->after('name');
            $table->unsignedSmallInteger('position')->default(1)->after('type');
            $table->date('starts_on')->nullable()->after('position');
            $table->date('ends_on')->nullable()->after('starts_on');

            $table->index(['academic_year_id', 'position']);
        });

        // Existing periods keep the order they were created in.
        $position = [];

        foreach (DB::table('semesters')->orderBy('id')->get(['id', 'academic_year_id']) as $semester) {
            $key = (string) $semester->academic_year_id;
            $position[$key] = ($position[$key] ?? 0) + 1;

            DB::table('semesters')->where('id', $semester->id)->update(['position' => $position[$key]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // MySQL keeps the index on `academic_year_id` when `position` goes,
        // and the foreign key still needs it. Do not drop it by hand.
        Schema::table('semesters', function (Blueprint $table): void {
            $table->dropColumn(['type', 'position', 'starts_on', 'ends_on']);
        });

        Schema::table('academic_years', function (Blueprint $table): void {
            $table->dropColumn(['starts_on', 'ends_on']);
        });
    }
};
