<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * Every enrollment state change is written here and never changed again.
     * The table answers "who moved this student, when, and why".
     */
    public function up(): void
    {
        Schema::create('enrollment_status_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->date('effective_on');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500)->nullable();
            $table->timestamps();

            $table->index(['student_record_id', 'effective_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_status_changes');
    }
};
