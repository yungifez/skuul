<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\ExamSlot;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'semester_id',
        'start_date',
        'stop_date',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'start_date' => 'date:Y-m-d',
        'stop_date' => 'date:Y-m-d',
        'active' => 'boolean',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function examSlots()
    {
        return $this->hasMany(ExamSlot::class);
    }

    //accessor for start date
    public function getStartDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y/m/d');
    }

    //accessor for stop date
    public function getStopDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y/m/d');
    }

}
