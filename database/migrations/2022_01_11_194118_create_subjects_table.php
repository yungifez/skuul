<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('subjects', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('my_class_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            //corrected this unique key in 2022_09_02_141457_correct_unique_key_in_subjects_table
            $table->unique(['name', 'school_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
