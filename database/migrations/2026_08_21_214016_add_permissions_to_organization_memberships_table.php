<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * A null value means the member holds every organization permission. Only
     * a delegated member stores a list, so existing rows keep their authority.
     */
    public function up(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table): void {
            $table->json('permissions')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table): void {
            $table->dropColumn('permissions');
        });
    }
};
