<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * The gradebook is a tree: a subject holds categories, a category holds
     * grade items, and each student has one entry per item. Exams are one kind
     * of item, not the only way to grade.
     */
    public function up(): void
    {
        Schema::create('grade_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('grade_categories')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('aggregation', 30)->default('weighted_mean');
            $table->decimal('weight', 8, 3)->default(1);
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();
        });

        Schema::create('grade_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('grade_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->string('type', 20)->default('numeric');
            $table->decimal('max_points', 8, 2)->nullable();
            $table->decimal('weight', 8, 3)->default(1);
            $table->date('due_on')->nullable();
            $table->unsignedSmallInteger('position')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('grade_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('grade_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('state', 20)->default('graded');
            $table->decimal('points', 8, 2)->nullable();
            $table->string('scale_value', 50)->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            // One student holds one entry per item.
            $table->unique(['grade_item_id', 'student_record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
        Schema::dropIfExists('grade_items');
        Schema::dropIfExists('grade_categories');
    }
};
