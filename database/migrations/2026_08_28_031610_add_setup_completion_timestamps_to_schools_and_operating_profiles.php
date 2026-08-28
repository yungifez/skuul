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
        Schema::table('schools', function (Blueprint $table): void {
            $table->timestamp('setup_details_completed_at')->nullable()->after('updated_at');
        });

        Schema::table('school_operating_profiles', function (Blueprint $table): void {
            $table->timestamp('setup_completed_at')->nullable()->after('updated_at');
        });

        $now = now();

        DB::table('schools')
            ->whereNotNull('name')
            ->whereNotNull('address')
            ->whereNotNull('country')
            ->whereNotNull('state')
            ->whereNotNull('city')
            ->whereNotNull('postal_code')
            ->update(['setup_details_completed_at' => $now]);

        DB::table('school_operating_profiles')
            ->whereColumn('updated_at', '>', 'created_at')
            ->update(['setup_completed_at' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_operating_profiles', function (Blueprint $table): void {
            $table->dropColumn('setup_completed_at');
        });

        Schema::table('schools', function (Blueprint $table): void {
            $table->dropColumn('setup_details_completed_at');
        });
    }
};
