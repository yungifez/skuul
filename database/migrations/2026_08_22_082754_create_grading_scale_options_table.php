<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grading_scale_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained()->cascadeOnDelete();
            $table->string('label', 100);
            $table->decimal('points', 8, 2)->nullable();
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();

            $table->unique(['grading_scale_id', 'label']);
            $table->index(['grading_scale_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_scale_options');
    }
};
