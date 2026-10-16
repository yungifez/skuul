<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Closing or reopening a period is written here and never changed again.
     */
    public function up(): void
    {
        Schema::create('academic_period_status_changes', function (Blueprint $table): void {
            $table->id();
            $table->morphs('period');
            $table->string('from_status', 20);
            $table->string('to_status', 20);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_period_status_changes');
    }
};
