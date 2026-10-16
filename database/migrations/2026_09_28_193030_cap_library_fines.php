<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A book lost for a term would otherwise cost more than the book. The cap
     * is in minor units, and an empty cap keeps the old behaviour.
     */
    public function up(): void
    {
        Schema::table('library_lending_rules', function (Blueprint $table): void {
            $table->unsignedBigInteger('fine_cap')->nullable()->after('fine_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('library_lending_rules', function (Blueprint $table): void {
            $table->dropColumn('fine_cap');
        });
    }
};
