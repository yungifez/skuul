<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('exam_records', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('exam_slot_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('subject_id');
            $table->integer('student_marks')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'subject_id', 'section_id', 'exam_Slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_records');
    }
};
