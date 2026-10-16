<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the optional billing-style address fields to existing schools.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->string('address_line_2')->nullable()->after('address');
            $table->string('country', 100)->nullable()->after('email');
            $table->string('state', 100)->nullable()->after('country');
            $table->string('city', 100)->nullable()->after('state');
            $table->string('postal_code', 30)->nullable()->after('city');
        });
    }

    /**
     * Remove the billing-style address fields.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->dropColumn(['address_line_2', 'country', 'state', 'city', 'postal_code']);
        });
    }
};
