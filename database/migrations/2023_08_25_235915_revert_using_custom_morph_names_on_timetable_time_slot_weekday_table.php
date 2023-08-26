<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //update all existing subject columns
        DB::table('timetable_time_slot_weekday')
              ->where('timetable_time_slot_weekdayable_type', 'subject')
              ->update(['timetable_time_slot_weekdayable_type' => 'App\Models\Subject']);

        //update all existing custom timetable item columns
        DB::table('timetable_time_slot_weekday')
              ->where('timetable_time_slot_weekdayable_type', 'customTimetableItem')
              ->update(['timetable_time_slot_weekdayable_type' => 'App\Models\CustomTimetableItem']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //revert from App\Models\Subjects to subject
        DB::table('timetable_time_slot_weekday')
              ->where('timetable_time_slot_weekdayable_type', 'App\Models\Subject')
              ->update(['timetable_time_slot_weekdayable_type' => 'subject']);
        //revert from App\Models\CustomTimetableItem to customTimetableItem
        DB::table('timetable_time_slot_weekday')
              ->where('timetable_time_slot_weekdayable_type', 'App\Models\CustomTimetableItem')
              ->update(['timetable_time_slot_weekdayable_type' => 'customTimetableItem']);
    }
};
