<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Attendance is one record per student per day, and one more per lesson
     * when the school takes a lesson register. A correction never overwrites
     * the record; it writes the change to the history beside it.
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('my_class_id')->nullable()->constrained('my_classes')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 10)->default('daily');
            $table->date('attended_on');
            $table->string('status', 20)->default('not_recorded');
            $table->string('reason', 255)->nullable();
            $table->string('source', 30)->default('teacher');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            // One student holds one record per register, per day, per subject.
            $table->unique(['student_record_id', 'attended_on', 'kind', 'subject_id'], 'attendance_unique_per_register');
            $table->index(['school_id', 'attended_on']);
        });

        Schema::create('attendance_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 20);
            $table->string('to_status', 20);
            $table->string('reason', 500)->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_changes');
        Schema::dropIfExists('attendance_records');
    }
};
