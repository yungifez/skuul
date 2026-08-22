<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'name',
        'description',
        'academic_period_id',
        'start_date',
        'stop_date',
        'active',
        'publish_result',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'start_date' => 'date:Y-m-d',
        'stop_date' => 'date:Y-m-d',
        'active' => 'boolean',
        'publish_result' => 'boolean',
    ];

    /**
     * Get the academic period that owns the exam.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the slots of the exam.
     *
     * @return HasMany<ExamSlot, $this>
     */
    public function examSlots(): HasMany
    {
        return $this->hasMany(ExamSlot::class);
    }

    /**
     * Calculate total marks attainable in each subjects for an exam.
     *
     * @return int|string
     */
    public function getTotalAttainableMarksInASubjectAttribute()
    {
        $totalMarks = 0;
        foreach ($this->examSlots as $examSlot) {
            $totalMarks += $examSlot->total_marks;
        }

        return $totalMarks;
    }

    /**
     * Calculate total marks gotten by student in academic period across all exams in a subject.
     *
     *
     * @return int
     */
    public function calculateStudentTotalMarkInSubjectForAcademicPeriod(AcademicPeriod $academicPeriod, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInAcademicPeriodForSubject($academicPeriod, $user->id, $subject->id)->pluck('student_marks')->sum();
    }
}
