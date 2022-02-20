<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyClass;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_class_id',
        'new_class_id',
        'old_section_id',
        'new_section_id',
        'academic_year_id',
        'students',
        'school_id',
    ];

    protected $casts = [
        'students' => 'array',
    ];

    public function getLabelAttribute()
    {    
        return "{$this->oldClass->name} - {$this->oldSection->name} to {$this->newClass->name} - {$this->newSection->name} year: {$this->academicYear->start_year} - {$this->academicYear->stop_year}";
    }

    public function oldClass()
    {
        return $this->belongsTo(MyClass::class, 'old_class_id');
    }

    public function newClass()
    {
        return $this->belongsTo(MyClass::class, 'new_class_id');
    }

    public function oldSection()
    {
        return $this->belongsTo(Section::class, 'old_section_id');
    }

    public function newSection()
    {
        return $this->belongsTo(Section::class, 'new_section_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
