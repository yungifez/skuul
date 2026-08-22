<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_offerings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('my_class_id')->constrained()->cascadeOnDelete();
            $table->string('roster_mode', 32)->default('home_section');
            $table->string('status', 20)->default('draft');
            $table->unsignedTinyInteger('planned_periods_per_week')->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->string('active_key', 64)->nullable()->unique();
            $table->timestamps();

            $table->index(['school_id', 'academic_period_id', 'status']);
            $table->index(['academic_year_id', 'my_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
