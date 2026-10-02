<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A timetable is now a revision with a state. Publishing freezes it, and
     * the next change starts a new revision, so the schedule people already
     * read stays as it was.
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->string('status', 20)->default('draft')->after('description');
            $table->unsignedSmallInteger('revision')->default(1)->after('status');
            $table->foreignId('section_id')->nullable()->after('my_class_id')->constrained()->nullOnDelete();
            $table->date('effective_from')->nullable()->after('section_id');
            $table->date('effective_to')->nullable()->after('effective_from');
            $table->timestamp('published_at')->nullable()->after('effective_to');
            $table->foreignId('published_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
            $table->foreignId('revision_of_id')->nullable()->after('published_by')->constrained('timetables')->nullOnDelete();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropForeign(['revision_of_id']);
            $table->dropForeign(['published_by']);
            $table->dropForeign(['section_id']);
        });

        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropColumn([
                'status',
                'revision',
                'section_id',
                'effective_from',
                'effective_to',
                'published_at',
                'published_by',
                'revision_of_id',
            ]);
        });
    }
};
