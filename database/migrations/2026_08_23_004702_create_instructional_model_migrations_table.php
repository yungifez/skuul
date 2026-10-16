<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every deliberate move of a running cycle to another instructional model.
 *
 * Choosing the model of a cycle that has not started is a setting. Moving a
 * cycle learners already work in is not: it changes what staff are asked for
 * while the answers already given stay as they are. That move is recorded
 * here, with the reason and what the cycle held at the time.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instructional_model_migrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('from_model')->nullable();
            $table->string('to_model');
            $table->text('reason');
            $table->json('impact')->nullable();
            $table->foreignId('migrated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Named, because the generated name for these two columns runs
            // past the 64 characters MySQL allows an identifier.
            $table->index(['school_id', 'academic_year_id'], 'imodel_migrations_cycle_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructional_model_migrations');
    }
};
