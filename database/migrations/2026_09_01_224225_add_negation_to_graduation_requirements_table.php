<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('graduation_requirements', function (Blueprint $table): void {
            $table->boolean('is_negated')->default(false)->after('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_requirements', function (Blueprint $table): void {
            $table->dropColumn('is_negated');
        });
    }
};
