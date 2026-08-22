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
        Schema::create('assessment_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('assessment_template_category_id')->nullable();
            $table->string('name', 150);
            $table->string('type', 20)->default('numeric');
            $table->foreignId('grading_scale_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('max_points', 8, 2)->nullable();
            $table->decimal('weight', 8, 3)->default(1);
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();

            $table->index(['assessment_template_id', 'position'], 'assessment_template_items_template_position_index');
            $table->foreign('assessment_template_category_id', 'assessment_template_items_category_foreign')
                ->references('id')
                ->on('assessment_template_categories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_template_items');
    }
};
