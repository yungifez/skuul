<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section_id',
        'subject_id',
        'exam_slot_id',
        'student_marks',
    ];

    /**
     * Get the subject that owns the ExamRecord.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the examSlot that owns the ExamRecord.
     */
    public function examSlot(): BelongsTo
    {
        return $this->belongsTo(ExamSlot::class);
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
