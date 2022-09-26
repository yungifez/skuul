<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('promotions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('old_class_id')->constrained('my_classes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('new_class_id')->constrained('my_classes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('old_section_id')->constrained('sections')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('new_section_id')->constrained('sections')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade')->onUpdate('cascade');
            $table->json('students');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
