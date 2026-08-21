<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One student excused from one requirement, and why.
 */
class GraduationExemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'graduation_requirement_id',
        'student_record_id',
        'reason',
        'granted_by',
    ];

    /**
     * Get the requirement the student is excused from.
     *
     * @return BelongsTo<GraduationRequirement, $this>
     */
    public function graduationRequirement(): BelongsTo
    {
        return $this->belongsTo(GraduationRequirement::class);
    }

    /**
     * Get the enrollment that is excused.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }
}
