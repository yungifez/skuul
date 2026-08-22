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

    protected $fillable = ['academic_cycle_section_id'];

    /**
     * Get the studentRecord that owns the AcademicYearStudentRecord.
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class, 'student_record_id', 'id');
    }

    /**
     * Get the cycle section that owns the AcademicYearStudentRecord.
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }
}
