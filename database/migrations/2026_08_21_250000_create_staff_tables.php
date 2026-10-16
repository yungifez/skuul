<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Employment is not teaching. A teaching assignment says who teaches what;
     * these tables say who works here, what they are qualified for, when they
     * are free, and when they are away.
     */
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('staff_number', 30)->nullable();
            $table->string('job_title', 150)->nullable();
            $table->string('department', 150)->nullable();
            $table->string('employment_type', 20)->default('full_time');
            $table->string('status', 20)->default('active');
            $table->date('joined_on')->nullable();
            $table->date('left_on')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'user_id']);
            $table->unique(['school_id', 'staff_number']);
        });

        Schema::create('staff_credentials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('staff_profile_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('name', 200);
            $table->string('issuer', 200)->nullable();
            $table->string('reference', 100)->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('staff_availabilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('staff_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->timestamps();

            $table->index(['staff_profile_id', 'day_of_week']);
        });

        Schema::create('staff_leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_profile_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('annual');
            $table->string('status', 20)->default('requested');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('reason', 500)->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 500)->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        Schema::create('staff_leave_status_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('staff_leave_request_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('staff_leave_status_changes');
        Schema::dropIfExists('staff_leave_requests');
        Schema::dropIfExists('staff_availabilities');
        Schema::dropIfExists('staff_credentials');
        Schema::dropIfExists('staff_profiles');
    }
};
