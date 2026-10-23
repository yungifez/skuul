<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Let a lesson register belong to one course offering and one lesson time.
 *
 * A campus where learners move between rooms takes a register in each
 * lesson. Two offerings of one subject, or two lessons of one subject on a
 * day, each need a register of their own, so the subject alone no longer
 * names a register.
 *
 * Each record now carries the key of its register. The old unique index
 * also let a learner hold two daily records on one day, because its subject
 * column is null there. Such rows are kept: the newest one becomes the
 * day's register and each older one keeps a key of its own.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->foreignId('course_offering_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            $table->foreignId('timetable_time_slot_id')->nullable()->after('course_offering_id')->constrained()->nullOnDelete();
            $table->string('register_key', 80)->nullable()->after('kind');
        });

        DB::table('attendance_records')->where('kind', 'daily')->update(['register_key' => DB::raw("CONCAT('daily:', id)")]);
        DB::table('attendance_records')
            ->whereIn('id', fn ($newest) => $newest->select('id')->fromSub(
                DB::table('attendance_records')->selectRaw('MAX(id) as id')->where('kind', 'daily')->groupBy('student_record_id', 'attended_on'),
                'newest',
            ))
            ->update(['register_key' => 'daily']);
        DB::table('attendance_records')->where('kind', '!=', 'daily')->whereNotNull('subject_id')->update(['register_key' => DB::raw("CONCAT('subject:', subject_id)")]);
        DB::table('attendance_records')->whereNull('register_key')->update(['register_key' => DB::raw("CONCAT('record:', id)")]);

        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->string('register_key', 80)->nullable(false)->change();

            // The new index leads with the student, so the student foreign
            // key moves to it before the old index goes.
            $table->unique(['student_record_id', 'attended_on', 'register_key'], 'attendance_one_record_per_register');
            $table->dropUnique('attendance_unique_per_register');
            $table->index(['course_offering_id', 'attended_on']);
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->unique(['student_record_id', 'attended_on', 'kind', 'subject_id'], 'attendance_unique_per_register');
            $table->dropUnique('attendance_one_record_per_register');

            // MySQL holds a foreign key to the index that leads with its
            // column, so the keys go before the index.
            $table->dropForeign(['timetable_time_slot_id']);
            $table->dropForeign(['course_offering_id']);
            $table->dropIndex(['course_offering_id', 'attended_on']);
            $table->dropColumn(['timetable_time_slot_id', 'course_offering_id', 'register_key']);
        });
    }
};
