<?php

namespace App\Models;

use App\Enums\AcademicStructureStatus;
use App\Traits\InSchool;
use Database\Factories\AcademicCycleSectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A home section that exists for one exact academic cycle.
 *
 * A section is never reused for another cycle.
 *
 * @property AcademicStructureStatus $status
 * @property string $name
 */
class AcademicCycleSection extends Model
{
    /** @use HasFactory<AcademicCycleSectionFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_level_id',
        'homeroom_teacher_id',
        'name',
        'label',
        'stream',
        'shift',
        'language',
        'room',
        'capacity',
        'position',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => AcademicStructureStatus::Draft->value,
        'position' => 0,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'position' => 'integer',
        'status' => AcademicStructureStatus::class,
    ];

    /**
     * Answer whether the setup of this section may still change.
     *
     * An archived section, and a section of a closed cycle, are kept for
     * history only. Load `academicYear` before asking this in a list.
     */
    public function isEditable(): bool
    {
        return $this->status !== AcademicStructureStatus::Archived
            && !$this->academicYear->isClosed();
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<AcademicLevel, $this>
     */
    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Get every placement record that names this exact cycle section.
     *
     * @return HasMany<EnrollmentPlacement, $this>
     */
    public function placementRecords(): HasMany
    {
        return $this->hasMany(EnrollmentPlacement::class);
    }

    /**
     * Get enrollments whose current placement names this cycle section.
     *
     * @return HasMany<StudentRecord, $this>
     */
    public function currentEnrollments(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }

    /**
     * Get timetable revisions for this exact home group.
     *
     * @return HasMany<Timetable, $this>
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}
