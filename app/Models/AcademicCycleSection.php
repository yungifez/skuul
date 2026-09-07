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
 * @property ?string $label
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
     * Write the name this section is known by.
     *
     * A section carries an optional `label` that the school writes itself.
     * Where it has none, its `name` stands in.
     */
    public function displayName(): string
    {
        return $this->label ?: $this->name;
    }

    /**
     * Write the name of this section with the class it sits in.
     *
     * A section is named "A" or "B", which says nothing on its own: every
     * class has an A. Use this anywhere the class is not already beside it,
     * such as a select, a checkbox list, or a column that stands alone.
     *
     * Load `academicLevel` before calling this in a list.
     */
    public function qualifiedName(): string
    {
        $level = $this->academicLevel?->name;

        return $level === null ? $this->displayName() : $level.' · '.$this->displayName();
    }

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
