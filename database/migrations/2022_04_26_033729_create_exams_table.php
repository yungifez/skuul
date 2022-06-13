<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('exams', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->foreignId('semester_id')->references('id')->on('semesters')->onDelete('cascade')->onUpdate('cascade');
            $table->date('start_date');
            $table->date('stop_date');
            $table->boolean('active')->default(false);
            $table->boolean('publish_result')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
