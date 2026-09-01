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
        Schema::table('graduation_plans', function (Blueprint $table): void {
            $table->unsignedSmallInteger('required_count')->nullable()->after('completion_operator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_plans', function (Blueprint $table): void {
            $table->dropColumn('required_count');
        });
    }
};
