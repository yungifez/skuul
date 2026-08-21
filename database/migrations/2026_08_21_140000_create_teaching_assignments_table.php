<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * A teaching assignment says who teaches a subject, in which period, to
     * which section, in what part, and from when. Several teachers can share
     * one subject, so the assignment is a record of its own.
     */
    public function up(): void
    {
        Schema::create('teaching_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();

            // No section means the assignment covers the whole class.
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role', 20)->default('lead');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'subject_id', 'academic_year_id']);
            $table->index(['user_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_assignments');
    }
};
