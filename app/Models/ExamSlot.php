<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSlot extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = ['name', 'description', 'total_marks', 'exam_id'];

    /**
     * Get the exam that owns the slot.
     *
     * @return BelongsTo<Exam, $this>
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the gradebook assessments that record this paper.
     *
     * @return HasMany<GradeItem, $this>
     */
    public function gradeItems(): HasMany
    {
        return $this->hasMany(GradeItem::class);
    }

    /**
     * Get the academic period that governs this exam slot.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->exam?->academicPeriod;
    }
}
