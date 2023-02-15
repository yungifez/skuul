<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('academic_year_student_record', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->notNullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('student_record_id')->notNullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('my_class_id')->notNullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('section_id')->notNullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_year_student_record');
    }
};
