<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('legacy_my_class_id')->nullable()->constrained('my_classes')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('academic_levels')->nullOnDelete();
            $table->string('name');
            $table->string('label')->nullable();
            $table->string('code')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique('legacy_my_class_id');
            $table->unique(['school_id', 'name']);
            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'status', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_levels');
    }
};
