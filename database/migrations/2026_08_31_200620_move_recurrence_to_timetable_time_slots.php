<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_time_slots', function (Blueprint $table): void {
            $table->string('recurrence', 20)->default('weekly')->after('stop_time');
            $table->date('occurs_on')->nullable()->after('recurrence');
        });

        DB::table('timetable_time_slots')
            ->join('timetables', 'timetables.id', '=', 'timetable_time_slots.timetable_id')
            ->select([
                'timetable_time_slots.id',
                'timetables.recurrence',
                'timetables.occurs_on',
            ])
            ->orderBy('timetable_time_slots.id')
            ->get()
            ->each(function (object $slot): void {
                DB::table('timetable_time_slots')
                    ->where('id', $slot->id)
                    ->update([
                        'recurrence' => $slot->recurrence,
                        'occurs_on' => $slot->occurs_on,
                    ]);
            });

        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropColumn(['recurrence', 'occurs_on']);
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->string('recurrence', 20)->default('weekly')->after('academic_period_id');
            $table->date('occurs_on')->nullable()->after('recurrence');
        });

        Schema::table('timetable_time_slots', function (Blueprint $table): void {
            $table->dropColumn(['recurrence', 'occurs_on']);
        });
    }
};
