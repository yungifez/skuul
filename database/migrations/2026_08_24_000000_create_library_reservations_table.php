<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A queue for a title everybody wants.
 *
 * A reservation is against a title, not a copy: any copy of it will do. When
 * one comes back it is held for the person at the front of the queue for a few
 * days, so they are not beaten to the shelf by whoever happens to walk in.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('library_reservations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('library_title_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('status', 20)->default('waiting');
            $table->date('reserved_on');

            // The copy being kept back, and the day it goes back on the shelf
            // if nobody collects it.
            $table->foreignId('library_copy_id')->nullable()->constrained()->nullOnDelete();
            $table->date('ready_on')->nullable();
            $table->date('holds_until')->nullable();
            $table->date('closed_on')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'library_title_id', 'status']);
            $table->index(['school_id', 'user_id', 'status']);
        });

        Schema::table('library_lending_rules', function (Blueprint $table): void {
            // How long a copy waits on the hold shelf before the next person
            // in the queue gets it.
            $table->unsignedSmallInteger('hold_days')->default(3)->after('renewals_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('library_lending_rules', function (Blueprint $table): void {
            $table->dropColumn('hold_days');
        });

        Schema::dropIfExists('library_reservations');
    }
};
