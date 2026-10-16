<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A role a campus has stopped using.
 *
 * Deleting a role would take away, without a word, whatever the people holding
 * it could do. An archived role keeps its holders and its history but is never
 * offered again, so a campus can retire a role safely.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->timestamp('archived_at')->nullable()->after('guard_name');
            $table->string('description', 255)->nullable()->after('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn(['archived_at', 'description']);
        });
    }
};
