<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property AcademicCycleSection $sourceAcademicCycleSection
 * @property AcademicCycleSection $destinationAcademicCycleSection
 * @property AcademicYear         $academicYear
 */
class Promotion extends Model
{
    use HasFactory;
    use InAcademicPeriod;
    use InSchool;

    protected $fillable = [
        'source_academic_cycle_section_id',
        'destination_academic_cycle_section_id',
        'academic_year_id',
        'students',
        'school_id',
    ];

    protected $casts = [
        'students' => 'array',
    ];

    public function getLabelAttribute(): string
    {
        return "{$this->sourceAcademicCycleSection->name} to {$this->destinationAcademicCycleSection->name} · {$this->academicYear->start_year}-{$this->academicYear->stop_year}";
    }

    public function sourceAcademicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class, 'source_academic_cycle_section_id');
    }

    public function destinationAcademicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class, 'destination_academic_cycle_section_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
