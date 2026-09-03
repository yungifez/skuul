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
        Schema::table('dormitories', function (Blueprint $table): void {
            $table->foreignId('boarding_residence_id')->nullable()->after('school_id')
                ->constrained('boarding_residences')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dormitories', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('boarding_residence_id');
        });
    }
};
