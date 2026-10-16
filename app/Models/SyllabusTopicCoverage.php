<?php

namespace App\Models;

use App\Enums\TopicCoverageStatus;
use App\Traits\InAcademicPeriod;
use Database\Factories\SyllabusTopicCoverageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * What one class was actually taught of a planned topic.
 *
 * A null section means the offering is taught to one group, so it has one track.
 */
class SyllabusTopicCoverage extends Model
{
    /** @use HasFactory<SyllabusTopicCoverageFactory> */
    use HasFactory;

    use InAcademicPeriod;

    protected $fillable = [
        'syllabus_topic_id', 'academic_cycle_section_id', 'status', 'covered_on', 'note', 'recorded_by',
    ];

    protected $casts = [
        'status' => TopicCoverageStatus::class,
        'covered_on' => 'date:Y-m-d',
    ];

    /**
     * @return BelongsTo<SyllabusTopic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(SyllabusTopic::class, 'syllabus_topic_id');
    }

    /**
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by')->withTrashed();
    }

    /**
     * Get the academic period that freezes this record.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->topic?->governingAcademicPeriod();
    }
}
