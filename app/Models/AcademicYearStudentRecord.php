<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AcademicYearStudentRecord extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = ['my_class_id', 'section_id'];

    /**
     * Get the studentRecord that owns the AcademicYearStudentRecord.
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class, 'student_record_id', 'id');
    }

    /**
     * Get the class that owns the AcademicYearStudentRecord.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(MyClass::class, 'my_class_id', 'id');
    }

    /**
     * Get the section that owns the AcademicYearStudentRecord.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
