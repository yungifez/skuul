<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('timetable_time_slot_weekday', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_time_slot_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('weekday_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->unique(['weekday_id', 'timetable_time_slot_id'], 'time_slot_weekday');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_time_slot_weekday');
    }
};
