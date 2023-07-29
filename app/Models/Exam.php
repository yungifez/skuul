<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'semester_id',
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
        'start_date'        => 'date:Y-m-d',
        'stop_date'         => 'date:Y-m-d',
        'active'            => 'boolean',
        'publish_result'    => 'boolean',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

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
     * Calculate total marks gotten by student in semester across all exams in a subject.
     *
     *
     * @return int
     */
    public function calculateStudentTotalMarkInSubjectForSemester(Semester $semester, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInSemesterForSubject($semester, $user->id, $subject->id)->pluck('student_marks')->sum();
    }
}
