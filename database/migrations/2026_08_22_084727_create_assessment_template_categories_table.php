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
        Schema::create('assessment_template_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('assessment_template_categories')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('aggregation', 30)->default('weighted_mean');
            $table->decimal('weight', 8, 3)->default(1);
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();

            $table->index(['assessment_template_id', 'position'], 'assessment_template_categories_template_position_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_template_categories');
    }
};
