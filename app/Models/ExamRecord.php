<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRecord extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'user_id',
        'section_id',
        'subject_id',
        'exam_slot_id',
        'student_marks',
    ];

    /**
     * Get the subject that owns the ExamRecord.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the examSlot that owns the ExamRecord.
     *
     * @return BelongsTo<ExamSlot, $this>
     */
    public function examSlot(): BelongsTo
    {
        return $this->belongsTo(ExamSlot::class);
    }

    /**
     * Get the period that governs this mark.
     *
     * A mark belongs to the academic period of the exam it was entered for.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->examSlot?->exam?->academicPeriod;
    }

    public function scopeinSubject($query, $subject_id)
    {
        return $query->where('subject_id', $subject_id);
    }

    public function scopeinSection($query, $section_id)
    {
        return $query->where('section_id', $section_id);
    }
}
