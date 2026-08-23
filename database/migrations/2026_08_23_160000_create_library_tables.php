<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A title is what a book is. A copy is the object on one campus's shelf.
     * A school group describes a book once and every campus lends its own
     * copies, the way subjects already work.
     */
    public function up(): void
    {
        Schema::create('library_titles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('authors', 255)->nullable();
            $table->string('isbn', 20)->nullable();
            $table->string('category', 80)->nullable();
            $table->year('published_year')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'title'], 'library_titles_name_index');
            $table->index(['isbn']);
        });

        Schema::create('library_copies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('library_title_id')->constrained()->cascadeOnDelete();

            // The barcode is what somebody scans at the desk, so it has to be
            // unique on the campus that owns the copy.
            $table->string('barcode', 60);
            $table->string('status', 20)->default('on_shelf');
            $table->string('shelf_mark', 60)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'barcode']);
            $table->index(['library_title_id']);
        });

        Schema::create('library_lending_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('loan_days')->default(14);
            $table->unsignedSmallInteger('learner_limit')->default(3);
            $table->unsignedSmallInteger('staff_limit')->default(10);
            $table->unsignedSmallInteger('renewals_allowed')->default(1);

            // Minor units a day, like every other money column.
            $table->unsignedBigInteger('fine_per_day')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id']);
        });

        Schema::create('library_loans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('library_copy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('issued_on');
            $table->date('due_on');
            $table->unsignedSmallInteger('renewals')->default(0);
            $table->date('returned_on')->nullable();
            $table->unsignedBigInteger('fine_charged')->default(0);
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'returned_on'], 'library_loans_open_index');
            $table->index(['user_id']);
            $table->index(['library_copy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_loans');
        Schema::dropIfExists('library_lending_rules');
        Schema::dropIfExists('library_copies');
        Schema::dropIfExists('library_titles');
    }
};
