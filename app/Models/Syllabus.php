<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'name', 'description', 'file', 'subject_id', 'academic_period_id',
    ];

    /**
     * Get the subject that owns the Syllabus.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the academic period this syllabus version belongs to.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }
}
