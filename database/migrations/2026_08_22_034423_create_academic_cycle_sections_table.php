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
        Schema::create('academic_cycle_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('legacy_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('label')->nullable();
            $table->string('stream')->nullable();
            $table->string('shift')->nullable();
            $table->string('language')->nullable();
            $table->string('room')->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id', 'academic_level_id', 'name'], 'academic_cycle_sections_identity_unique');
            $table->index(['school_id', 'academic_year_id', 'status', 'position'], 'academic_cycle_sections_listing_index');
            $table->index('legacy_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_cycle_sections');
    }
};
