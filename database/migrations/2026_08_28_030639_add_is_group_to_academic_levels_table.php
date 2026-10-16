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
        Schema::table('academic_levels', function (Blueprint $table): void {
            $table->boolean('is_group')->default(false)->after('parent_id');
        });

        DB::table('academic_levels')
            ->whereIn('id', DB::table('academic_levels')->whereNotNull('parent_id')->distinct()->pluck('parent_id'))
            ->update(['is_group' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_levels', function (Blueprint $table): void {
            $table->dropColumn('is_group');
        });
    }
};
