<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function oldClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class, 'old_class_id');
    }

    public function newClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class, 'new_class_id');
    }

    public function oldSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'old_section_id');
    }

    public function newSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'new_section_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
