<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('old_class_id')->constrained('my_classes');
            $table->foreignId('new_class_id')->constrained('my_classes');
            $table->foreignId('old_section_id')->constrained('sections');
            $table->foreignId('new_section_id')->constrained('sections');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('student_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
