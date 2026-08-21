<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * Health facts and support plans stay out of the student profile, because
     * everyone who reads a profile must not read these.
     */
    public function up(): void
    {
        Schema::create('student_health_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('blood_group', 10)->nullable();
            $table->text('conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('dietary_needs')->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('student_record_id');
        });

        Schema::create('support_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('category', 20)->default('intervention');
            $table->string('status', 20)->default('draft');
            $table->boolean('is_confidential')->default(false);
            $table->string('title', 255);
            $table->text('summary')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('review_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        Schema::create('support_plan_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_plan_id')->constrained()->cascadeOnDelete();
            $table->string('description', 500);
            $table->date('due_on')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('support_plan_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_plan_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->foreignId('written_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('support_plan_status_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_plan_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('support_plan_status_changes');
        Schema::dropIfExists('support_plan_notes');
        Schema::dropIfExists('support_plan_actions');
        Schema::dropIfExists('support_plans');
        Schema::dropIfExists('student_health_records');
    }
};
