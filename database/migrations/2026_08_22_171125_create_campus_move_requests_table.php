<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A campus admin cannot move a student to another campus alone. The
     * request names the student, the two campuses, the home section they are
     * going to, and the day it takes effect. The receiving campus decides,
     * and a person with organization authority may decide as well.
     */
    public function up(): void
    {
        Schema::create('campus_move_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('to_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_cycle_section_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('requested');
            $table->string('reason', 500)->nullable();
            $table->date('effective_on')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 500)->nullable();
            $table->timestamps();

            $table->index(['to_school_id', 'status']);
            $table->index(['student_record_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_move_requests');
    }
};
