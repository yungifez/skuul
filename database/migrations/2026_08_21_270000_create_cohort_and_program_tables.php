<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * A cohort is a named group that is not a class. A programme is something
     * the school runs beside its lessons. A graduation plan says what a
     * student must finish. None of them replace enrollment, and none of them
     * are a field on the student.
     */
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('type', 30)->default('other');
            $table->string('description', 500)->nullable();
            $table->boolean('is_restricted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'name']);
        });

        Schema::create('cohort_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('joined_on')->nullable();
            $table->date('left_on')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['cohort_id', 'student_record_id']);
            $table->unique(['cohort_id', 'user_id']);
        });

        Schema::create('programs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('type', 30)->default('club');
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'name']);
        });

        Schema::create('program_participations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('requested');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('schedule', 255)->nullable();
            $table->string('note', 500)->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        Schema::create('graduation_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('description', 500)->nullable();
            $table->boolean('uses_credits')->default(false);
            $table->unsignedSmallInteger('required_credits')->nullable();
            $table->foreignId('cohort_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'name']);
        });

        Schema::create('graduation_requirements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('graduation_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('description', 255);
            $table->unsignedSmallInteger('credits')->default(1);
            $table->decimal('pass_mark', 5, 2)->default(50);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        Schema::create('graduation_exemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('graduation_requirement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 500)->nullable();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['graduation_requirement_id', 'student_record_id'], 'graduation_exemption_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduation_exemptions');
        Schema::dropIfExists('graduation_requirements');
        Schema::dropIfExists('graduation_plans');
        Schema::dropIfExists('program_participations');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('cohort_members');
        Schema::dropIfExists('cohorts');
    }
};
