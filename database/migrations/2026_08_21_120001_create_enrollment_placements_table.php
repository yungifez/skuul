<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Every class and section a student sits in is written here once. The
     * newest record is where the student is now; the older ones say where the
     * student was and who moved them.
     */
    public function up(): void
    {
        Schema::create('enrollment_placements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('my_class_id')->constrained('my_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->date('effective_on');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500)->nullable();
            $table->timestamps();

            $table->index(['student_record_id', 'effective_on']);
            $table->index(['academic_year_id', 'my_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_placements');
    }
};
