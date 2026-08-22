<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use App\Traits\InSchool;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * One student enrollment in a school.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $school_id
 * @property bool $is_primary
 * @property EnrollmentStatus $status
 * @property string|null $admission_number
 * @property string|null $admission_date
 * @property int|null $my_class_id
 * @property int|null $section_id
 * @property int|null $academic_cycle_section_id
 */
class StudentRecord extends Model
{
    use HasFactory;
    use InSchool;

    protected static function booted(): void
    {
        static::saving(function (StudentRecord $enrollment): void {
            if (!$enrollment->is_primary || $enrollment->user_id === null) {
                return;
            }

            $query = static::query()->where('user_id', $enrollment->user_id);

            if ($enrollment->exists) {
                $query->whereKeyNot($enrollment->getKey());
            }

            $query->update(['is_primary' => false]);
        });
    }

    protected $fillable = [
        'admission_number',
        'admission_date',
        'my_class_id',
        'section_id',
        'academic_cycle_section_id',
        'user_id',
        'school_id',
        'status',
        'is_primary',
        'transferred_from_id',
    ];

    /**
     * The default values for a new enrollment.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => EnrollmentStatus::Active->value,
        'is_primary' => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admission_date' => 'datetime:Y-m-d',
        'status' => EnrollmentStatus::class,
        'is_primary' => 'boolean',
    ];

    /**
     * Limit the query to enrollments the student still attends.
     *
     * @param  Builder  $query
     */
    public function scopeAttending($query): Builder
    {
        return $query->where('status', EnrollmentStatus::Active);
    }

    /**
     * Limit the query to enrollments in one state.
     *
     * @param  Builder  $query
     */
    public function scopeWithStatus($query, EnrollmentStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Limit the query to the enrollment each student leads with.
     *
     * @param  Builder  $query
     */
    public function scopePrimary($query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Check if the student finished the program.
     */
    public function isGraduated(): bool
    {
        return $this->status === EnrollmentStatus::Graduated;
    }

    // accessor for admission_date

    public function getAdmissionDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * Get the class the student is placed in.
     *
     * @return BelongsTo<MyClass, $this>
     */
    public function myClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * Get the section the student is placed in.
     *
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the exact cycle section the student is currently placed in.
     *
     * This remains nullable while historical placements use legacy records.
     *
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * Get the person this enrollment belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get every recorded state change of this enrollment.
     */
    public function statusChanges(): HasMany
    {
        return $this->hasMany(EnrollmentStatusChange::class)->orderBy('effective_on')->orderBy('id');
    }

    /**
     * Get every class and section this enrollment held, oldest first.
     *
     * @return HasMany<EnrollmentPlacement, $this>
     */
    public function placements(): HasMany
    {
        return $this->hasMany(EnrollmentPlacement::class)->orderBy('effective_on')->orderBy('id');
    }

    /**
     * Get the placement the student holds now.
     *
     * @return HasOne<EnrollmentPlacement, $this>
     */
    public function currentPlacement(): HasOne
    {
        return $this->hasOne(EnrollmentPlacement::class)->ofMany(['effective_on' => 'max', 'id' => 'max']);
    }

    /**
     * Get the school this enrollment belongs to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the enrollment this one continues after a transfer.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function transferredFrom(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class, 'transferred_from_id');
    }

    /**
     * The academicYears that belong to the StudentRecord.
     */
    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(AcademicYear::class)
            ->as('studentAcademicYearBasedRecords')
            ->using(AcademicYearStudentRecord::class)
            ->withPivot('my_class_id', 'section_id', 'academic_cycle_section_id');
    }

    /**
     * Get current academic year.
     *
     * @return BelongsToMany
     */
    public function currentAcademicYear()
    {
        return $this->academicYears()->wherePivot('academic_year_id', current_academic_year()->id);
    }
}
