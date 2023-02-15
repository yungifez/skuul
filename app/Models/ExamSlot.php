<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get all of the examRecords for the ExamSlot.
     */
    public function examRecords(): HasMany
    {
        return $this->hasMany(ExamRecord::class);
    }
}
