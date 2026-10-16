<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A boarding place names a bed, not a room. Capacity is then the beds that
     * exist, which nobody has to keep up to date, and a house can always say
     * exactly where a child sleeps.
     */
    public function up(): void
    {
        Schema::create('dormitories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);

            // Schools do not agree on the word: house, hostel, block. The
            // record stays a dormitory; the screens read the campus word.
            $table->string('label', 40)->default('House');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'name']);
        });

        Schema::create('dormitory_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dormitory_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);
            $table->string('floor', 40)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['dormitory_id', 'name']);
        });

        Schema::create('dormitory_beds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dormitory_room_id')->constrained()->cascadeOnDelete();
            $table->string('name', 40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['dormitory_room_id', 'name']);
        });

        Schema::create('boarding_places', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();

            // No bed means the learner stopped boarding on this date. The
            // history is written forward, never rubbed out.
            $table->foreignId('dormitory_bed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->date('effective_on');
            $table->string('reason', 255)->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_record_id', 'effective_on'], 'boarding_places_student_index');
            $table->index(['dormitory_bed_id']);
        });

        Schema::create('boarding_supervisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dormitory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 40);
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['dormitory_id', 'starts_on'], 'boarding_supervisions_house_index');
        });

        Schema::create('overnight_leaves', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->date('leaves_on');
            $table->date('returns_on');
            $table->string('destination', 150);
            $table->string('contact', 100)->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('requested');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status', 'leaves_on'], 'overnight_leaves_tonight_index');
            $table->index(['student_record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overnight_leaves');
        Schema::dropIfExists('boarding_supervisions');
        Schema::dropIfExists('boarding_places');
        Schema::dropIfExists('dormitory_beds');
        Schema::dropIfExists('dormitory_rooms');
        Schema::dropIfExists('dormitories');
    }
};
