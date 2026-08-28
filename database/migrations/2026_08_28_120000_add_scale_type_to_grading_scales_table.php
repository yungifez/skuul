<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grading_scales', function (Blueprint $table): void {
            $table->string('scale_type')->default('percentage')->after('description');
            $table->decimal('maximum_value', 8, 2)->nullable()->after('scale_type');
        });

        $scalesWithPoints = fn () => DB::table('grading_scale_options')
            ->whereNotNull('points')
            ->select('grading_scale_id');

        DB::table('grading_scales')
            ->whereIn('id', $scalesWithPoints())
            ->update(['scale_type' => 'points']);

        DB::table('grading_scales')
            ->whereNotIn('id', $scalesWithPoints())
            ->update(['scale_type' => 'descriptive']);
    }

    public function down(): void
    {
        Schema::table('grading_scales', function (Blueprint $table): void {
            $table->dropColumn(['scale_type', 'maximum_value']);
        });
    }
};
