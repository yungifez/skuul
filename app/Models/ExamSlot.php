<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSlot extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'total_marks', 'exam_id'];

    /**
     * Get the exam that owns the ExamSlot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
