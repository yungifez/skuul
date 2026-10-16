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
        Schema::create('curriculum_outlines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'subject_id']);
        });

        Schema::create('curriculum_outline_topics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curriculum_outline_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('week')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('title');
            $table->text('objectives')->nullable();
            $table->text('content')->nullable();
            $table->text('resources')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_outline_topics');
        Schema::dropIfExists('curriculum_outlines');
    }
};
