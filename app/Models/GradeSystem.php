<?php

namespace App\Models;

use App\Models\ClassGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeSystem extends Model
{
    use HasFactory;

    protected $fillable = ['name','remark','grade_from','grade_to','class_group_id',];

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }
}
