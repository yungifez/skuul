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
        DB::table('grade_entries')
            ->whereIn('grade_item_id', DB::table('grade_items')->whereNull('course_offering_id')->select('id'))
            ->delete();

        DB::table('grade_items')->whereNull('course_offering_id')->delete();
        DB::table('grade_categories')->whereNull('course_offering_id')->delete();
        DB::table('result_snapshots')->whereNull('course_offering_id')->delete();

        Schema::table('grade_categories', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable(false)->change();
        });

        Schema::table('grade_items', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable(false)->change();
        });

        Schema::table('result_snapshots', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Legacy gradebook records cannot be restored after being discarded.');
    }
};
