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
        Schema::create('lesson_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('syllabus_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_cycle_section_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('week');
            $table->text('objectives');
            $table->text('activities');
            $table->text('evaluation')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note', 2000)->nullable();
            $table->timestamps();

            $table->index(['course_offering_id', 'academic_cycle_section_id', 'week'], 'lesson_notes_offering_section_week_index');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_notes');
    }
};
