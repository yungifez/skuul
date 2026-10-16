<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the optional billing-style address fields to existing user records.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('address_line_2')->nullable()->after('address');
            $table->string('country', 100)->nullable()->after('nationality');
            $table->string('postal_code', 30)->nullable()->after('city');
        });
    }

    /**
     * Remove the billing-style address fields.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['address_line_2', 'country', 'postal_code']);
        });
    }
};
