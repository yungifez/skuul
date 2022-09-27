<?php

namespace App\Models;

use App\Models\MyClass;
use App\Models\Section;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Get the studentRecord that owns the AcademicYearStudentRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class, 'student_record_id', 'id');
    }

    /**
     * Get the class that owns the AcademicYearStudentRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(MyClass::class, 'my_class_id', 'id');
    }

    /**
     * Get the section that owns the AcademicYearStudentRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
