<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('my_class_subject', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('my_class_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_class_subject');
    }
};
