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
        Schema::create('timetable_substitutions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->cascadeOnDelete();
            $table->foreignId('timetable_time_slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('weekday_id')->constrained()->cascadeOnDelete();
            $table->foreignId('replacement_teacher_id')->constrained('users')->restrictOnDelete();
            $table->date('substituted_on');
            $table->string('reason', 1000);
            $table->foreignId('approved_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['timetable_time_slot_id', 'weekday_id', 'substituted_on'], 'timetable_substitutions_slot_day_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_substitutions');
    }
};
