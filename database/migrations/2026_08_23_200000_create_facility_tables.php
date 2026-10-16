<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A school shares a hall, a laboratory, and a minibus between everybody
     * who wants them. Until now the only place a lesson could happen was the
     * section's own room, so a double booking was found by argument.
     */
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('kind', 30);
            $table->unsignedInteger('capacity')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'name']);
        });

        Schema::create('facility_bookings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('purpose', 255);
            $table->foreignId('booked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancelled_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'starts_at'], 'facility_bookings_when_index');
            $table->index(['school_id', 'starts_at'], 'facility_bookings_school_index');
        });

        // A lesson can be moved out of the section's own room for one entry.
        Schema::table('timetable_time_slot_weekday', function (Blueprint $table): void {
            $table->foreignId('facility_id')->nullable()->after('weekday_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetable_time_slot_weekday', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('facility_id');
        });

        Schema::dropIfExists('facility_bookings');
        Schema::dropIfExists('facilities');
    }
};
