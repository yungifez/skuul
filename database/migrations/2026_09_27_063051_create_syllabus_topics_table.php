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
        Schema::create('syllabus_topics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('syllabus_id')->constrained('syllabi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedSmallInteger('week')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('title');
            $table->text('objectives')->nullable();
            $table->text('content')->nullable();
            $table->text('resources')->nullable();
            $table->timestamps();

            $table->index(['syllabus_id', 'week', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabus_topics');
    }
};
