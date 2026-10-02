<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An import is checked before it is written, and every row keeps what it
     * wrote. `imported_records` remembers which outside identifier made which
     * record, so importing the same file twice changes it instead of copying
     * it.
     */
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('status', 20)->default('draft');
            $table->string('source_name', 255)->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->unsignedInteger('valid_count')->default(0);
            $table->unsignedInteger('invalid_count')->default(0);
            $table->unsignedInteger('applied_count')->default(0);
            $table->timestamp('applied_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'type']);
        });

        Schema::create('import_rows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('line_number');
            $table->string('source_id', 100)->nullable();
            $table->json('payload');
            $table->string('state', 20)->default('pending');
            $table->json('errors')->nullable();
            $table->nullableMorphs('subject');
            $table->timestamps();

            $table->index(['import_batch_id', 'state']);
        });

        Schema::create('imported_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('source_id', 100);
            $table->morphs('subject');
            $table->timestamps();

            $table->unique(['school_id', 'type', 'source_id'], 'imported_records_source_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_records');
        Schema::dropIfExists('import_rows');
        Schema::dropIfExists('import_batches');
    }
};
