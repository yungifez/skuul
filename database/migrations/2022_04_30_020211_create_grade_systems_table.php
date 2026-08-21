<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('grade_systems', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('remark')->nullable();
            $table->string('grade_from');
            $table->string('grade_till');
            $table->foreignId('class_group_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_systems');
    }
};
