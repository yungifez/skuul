<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_year',
        'stop_year',
        'school_id',
    ];

    public function name()
    {
        return "$this->start_year - $this->stop_year";
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    //semesters
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
}
