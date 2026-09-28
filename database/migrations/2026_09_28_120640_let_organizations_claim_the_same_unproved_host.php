<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A claim nobody has proved no longer keeps the address from others. Only
     * a proved address is unique, so an organization cannot hold another's
     * web address by claiming it first and never proving it.
     */
    public function up(): void
    {
        Schema::table('school_domains', function (Blueprint $table): void {
            $table->dropUnique(['host']);
            $table->unique(['organization_id', 'host']);
            $table->string('verified_host')->nullable()->storedAs('IF(verified_at IS NULL, NULL, host)');
            $table->unique('verified_host');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_domains', function (Blueprint $table): void {
            $table->dropUnique(['verified_host']);
            $table->dropColumn('verified_host');
            $table->dropUnique(['organization_id', 'host']);
            $table->unique('host');
        });
    }
};
