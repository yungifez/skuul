<?php

namespace App\Models;

use App\Traits\InSchool;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'title',
        'content',
        'attachment',
        'start_date',
        'stop_date',
        'active',
        'school_id',
    ];

    public function scopeActive($query)
    {
        $query->where('start_date', '<=', date('Y-m-d'))
            ->where('stop_date', '>=', date('Y-m-d'))
            ->where('active', 1);
    }

    // used in view for displaying time on datatable
    public function getStartDateForHumansAttribute()
    {
        return Carbon::parse($this->start_date)->diffForHumans();
    }

    // used in view for displaying time on datatable
    public function getStopDateForHumansAttribute()
    {
        return Carbon::parse($this->stop_date)->diffForHumans();
    }
}
