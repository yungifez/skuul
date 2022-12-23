<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'stop_time',
        'timetable_id',
    ];

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    //many to many relationship with week days
    public function weekdays()
    {
        //get pivot table as timetableRecords
        return $this->belongsToMany(Weekday::class)->as('timetableRecord')->withPivot(['timetable_time_slot_weekdayable_id', 'timetable_time_slot_weekdayable_type'])->withTimestamps()->using(TimetableRecord::class);
    }
}
