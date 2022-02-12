<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_class_id',
        'new_class_id',
        'old_section_id',
        'new_section_id',
        'academic_year_id',
        'student_id',
    ];
}
