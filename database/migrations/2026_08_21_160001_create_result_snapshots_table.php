<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * A published result is a copy of what the gradebook said at that moment.
     * Later corrections publish the next revision; the earlier one stays as it
     * was, because families and other schools may already have read it.
     */
    public function up(): void
    {
        Schema::create('result_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('revision')->default(1);
            $table->decimal('percentage', 6, 2)->nullable();
            $table->json('payload');
            $table->string('reason', 500)->nullable();
            $table->timestamp('published_at');
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['student_record_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_snapshots');
    }
};
