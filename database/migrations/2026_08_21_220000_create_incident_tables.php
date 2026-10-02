<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A case records what happened, who was there, what the school did, and
     * how the case moved. Safeguarding cases sit in the same tables but are
     * marked restricted, so only the people who handle them can read them.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 30);
            $table->string('category', 20)->default('behaviour');
            $table->string('status', 20)->default('reported');
            $table->boolean('is_restricted')->default(false);
            $table->string('summary', 255);
            $table->text('description')->nullable();
            $table->string('location', 150)->nullable();
            $table->timestamp('occurred_at');
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'reference']);
            $table->index(['school_id', 'status']);
        });

        Schema::create('incident_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_record_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role', 20)->default('subject');
            $table->string('note', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('incident_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('description', 500);
            $table->date('due_on')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('incident_status_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('incident_status_changes');
        Schema::dropIfExists('incident_actions');
        Schema::dropIfExists('incident_participants');
        Schema::dropIfExists('incidents');
    }
};
