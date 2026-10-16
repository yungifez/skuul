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
        Schema::table('syllabus_topics', function (Blueprint $table): void {
            $table->foreignId('copied_from_id')->nullable()->after('syllabus_id')->constrained('syllabus_topics')->nullOnDelete();
        });

        Schema::create('syllabus_topic_coverages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('syllabus_topic_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('academic_cycle_section_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
            $table->date('covered_on')->nullable();
            $table->string('note', 1000)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['syllabus_topic_id', 'academic_cycle_section_id'], 'syllabus_topic_coverages_topic_section_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabus_topic_coverages');

        Schema::table('syllabus_topics', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('copied_from_id');
        });
    }
};
