<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Database\Factories\SyllabusTopicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One planned topic in a syllabus: what is taught, in which week, and why.
 */
class SyllabusTopic extends Model
{
    /** @use HasFactory<SyllabusTopicFactory> */
    use HasFactory;

    use InAcademicPeriod;

    protected $fillable = [
        'syllabus_id', 'week', 'position', 'title', 'objectives', 'content', 'resources',
    ];

    protected $casts = [
        'week' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Get the syllabus this topic belongs to.
     *
     * @return BelongsTo<Syllabus, $this>
     */
    public function syllabus(): BelongsTo
    {
        return $this->belongsTo(Syllabus::class);
    }

    /**
     * Get the academic period that freezes this topic.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->syllabus?->governingAcademicPeriod();
    }
}
