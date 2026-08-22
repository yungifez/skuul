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
 * @property string|null                     $label
 * @property int|null                        $academic_year_id
 * @property int|null                        $parent_id
 * @property int                             $school_id
 * @property AcademicYear|null               $academicYear
 * @property self|null                       $parent
 */
class AcademicPeriod extends Model
{
    use HasFactory;
    use HasPeriodLifecycle;
    use InSchool;

    protected $table = 'academic_periods';

    protected $fillable = [
        'name',
        'label',
        'type',
        'position',
        'starts_on',
        'ends_on',
        'school_id',
        'academic_year_id',
        'parent_id',
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
                ->where('parent_id', $period->getAttribute('parent_id'))
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
     * Limit the query to periods that divide the cycle itself.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Limit the query to periods of the given kinds.
     *
     * @param Builder<$this>              $query
     * @param array<int, AcademicPeriodType> $types
     *
     * @return Builder<$this>
     */
    public function scopeOfType(Builder $query, array $types): Builder
    {
        return $query->whereIn('type', array_map(fn (AcademicPeriodType $type): string => $type->value, $types));
    }

    /**
     * Limit the query to periods that cover the given day.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeCovering(Builder $query, DateTimeInterface|string|null $date = null): Builder
    {
        $day = Carbon::parse($date ?? now())->toDateString();

        return $query->whereNotNull('starts_on')
            ->whereNotNull('ends_on')
            ->whereDate('starts_on', '<=', $day)
            ->whereDate('ends_on', '>=', $day);
    }

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
     * Views read it as `$academicPeriod->typeLabel`, so it is an attribute
     * rather than a method.
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(get: fn (): string => $this->type->label());
    }

    /**
     * Get the name to show, preferring what the school calls this period.
     *
     * A school that runs a "Rainy Session" wants to read that word, not the
     * type the application files it under.
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(get: fn (): string => $this->label ?? $this->name);
    }

    /**
     * Count the days the period runs, or null when it has no dates.
     */
    public function lengthInDays(): ?int
    {
        if ($this->starts_on === null || $this->ends_on === null) {
            return null;
        }

        return $this->starts_on->diffInDays($this->ends_on) + 1;
    }

    /**
     * Check if the school teaches during this period.
     *
     * A holiday inside a term is still inside the term, so ask the period,
     * not the term around it.
     */
    public function isTeachingPeriod(): bool
    {
        return $this->type->isTeaching();
    }

    /**
     * Get the period this one sits inside, when it sits inside one.
     *
     * @return BelongsTo<self, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the periods inside this one, in order.
     *
     * @return HasMany<self, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    /**
     * Get the academic year that owns the academic period.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the school that owns the academic period.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all of the exams for the AcademicPeriod.
     *
     * @return HasMany<Exam, $this>
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'academic_period_id');
    }

    /**
     * Get all of the examSlots for the AcademicPeriod.
     *
     * @return HasManyThrough<ExamSlot, Exam, $this>
     */
    public function examSlots(): HasManyThrough
    {
        return $this->hasManyThrough(ExamSlot::class, Exam::class, 'academic_period_id');
    }
}
