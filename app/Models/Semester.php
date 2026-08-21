<?php

namespace App\Models;

use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Traits\HasPeriodLifecycle;
use App\Traits\InSchool;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property AcademicPeriodStatus            $status
 * @property AcademicPeriodType              $type
 * @property int                             $position
 * @property \Illuminate\Support\Carbon|null $starts_on
 * @property \Illuminate\Support\Carbon|null $ends_on
 * @property string                          $name
 * @property int|null                        $academic_year_id
 * @property int                             $school_id
 * @property AcademicYear|null               $academicYear
 */
class Semester extends Model
{
    use HasFactory;
    use HasPeriodLifecycle;
    use InSchool;

    protected $table = 'semesters';

    protected $fillable = [
        'name',
        'type',
        'position',
        'starts_on',
        'ends_on',
        'school_id',
        'academic_year_id',
        'status',
    ];

    /**
     * The default values for a new period.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => AcademicPeriodStatus::Open->value,
        'type'   => AcademicPeriodType::Semester->value,
    ];

    /**
     * Give a new period the next place in its academic year.
     *
     * A period that nobody ordered still has to come after the last one, or
     * the calendar reads in the order rows happened to be written.
     */
    protected static function booted(): void
    {
        static::creating(function (self $period): void {
            $academicYearId = $period->getAttribute('academic_year_id');

            if (array_key_exists('position', $period->getAttributes()) || $academicYearId === null) {
                return;
            }

            $period->position = (int) static::query()
                ->where('academic_year_id', $academicYearId)
                ->max('position') + 1;
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'    => AcademicPeriodStatus::class,
        'type'      => AcademicPeriodType::class,
        'position'  => 'integer',
        'starts_on' => 'date:Y-m-d',
        'ends_on'   => 'date:Y-m-d',
    ];

    /**
     * Read the periods in the order the school teaches them.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * Check if the period covers the given day.
     *
     * A period with no dates covers nothing, because nobody said when it runs.
     */
    public function covers(DateTimeInterface|string|null $date = null): bool
    {
        if ($this->starts_on === null || $this->ends_on === null) {
            return false;
        }

        $day = Carbon::parse($date ?? now())->startOfDay();

        return $day->betweenIncluded($this->starts_on->startOfDay(), $this->ends_on->startOfDay());
    }

    /**
     * Get the label people read for this period.
     *
     * Views read it as `$semester->typeLabel`, so it is an attribute rather
     * than a method.
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(get: fn (): string => $this->type->label());
    }

    /**
     * Get the academic year that owns the semester.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the school that owns the semester.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all of the exams for the Semester.
     *
     * @return HasMany<Exam, $this>
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'semester_id');
    }

    /**
     * Get all of the examSlots for the Semester.
     *
     * @return HasManyThrough<ExamSlot, Exam, $this>
     */
    public function examSlots(): HasManyThrough
    {
        return $this->hasManyThrough(ExamSlot::class, Exam::class, 'semester_id');
    }
}
