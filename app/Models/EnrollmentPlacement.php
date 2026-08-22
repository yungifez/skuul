<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One class and section a student was placed in.
 *
 * The record is written once. Moving a student to another class means writing
 * the next placement, so the school can always say where a student sat in any
 * academic year and who moved them.
 *
 * @property int $student_record_id
 * @property int $academic_year_id
 * @property int|null $academic_period_id
 * @property int $my_class_id
 * @property int|null $section_id
 * @property int|null $academic_cycle_section_id
 */
class EnrollmentPlacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_record_id',
        'academic_year_id',
        'academic_period_id',
        'my_class_id',
        'section_id',
        'academic_cycle_section_id',
        'effective_on',
        'changed_by',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'effective_on' => 'date:Y-m-d',
    ];

    /**
     * Keep the history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Placement history cannot be changed. Record the next placement instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Placement history cannot be deleted. Record the next placement instead.');
        });
    }

    /**
     * Get the enrollment this placement belongs to.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the academic year the placement applies to.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the academic period the placement applies to, when it names one.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the class the student was placed in.
     *
     * @return BelongsTo<MyClass, $this>
     */
    public function myClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * Get the section the student was placed in.
     *
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the cycle-specific section the student was placed in.
     *
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * Get the person who made the placement.
     *
     * @return BelongsTo<User, $this>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
