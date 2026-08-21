<?php

namespace App\Models;

use App\Enums\AcademicPeriodStatus;
use App\Traits\HasPeriodLifecycle;
use App\Traits\InSchool;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property AcademicPeriodStatus $status
 * @property string $name
 * @property int $start_year
 * @property int $stop_year
 * @property Carbon|null $starts_on
 * @property Carbon|null $ends_on
 * @property int $school_id
 */
class AcademicYear extends Model
{
    use HasFactory;
    use HasPeriodLifecycle;
    use InSchool;

    protected $appends = ['name'];

    protected $fillable = [
        'start_year',
        'stop_year',
        'starts_on',
        'ends_on',
        'school_id',
        'status',
    ];

    /**
     * The default values for a new period.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => AcademicPeriodStatus::Open->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => AcademicPeriodStatus::class,
        'starts_on' => 'date:Y-m-d',
        'ends_on' => 'date:Y-m-d',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "$this->start_year - $this->stop_year",
        );
    }

    /**
     * Get the school that owns the academic year.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the periods in the academic year, in teaching order.
     *
     * @return HasMany<Semester, $this>
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Get the period that covers the given day, when one does.
     */
    public function periodForDate(DateTimeInterface|string|null $date = null): ?Semester
    {
        return $this->semesters()->get()->first(fn (Semester $semester): bool => $semester->covers($date));
    }

    /**
     * Get all of the exams for the AcademicYear.
     *
     * @return HasManyThrough<Exam, Semester, $this>
     */
    public function exams(): HasManyThrough
    {
        return $this->hasManyThrough(Exam::class, Semester::class, 'academic_year_id', 'semester_id', 'id', 'id');
    }

    /**
     * The studentRecords that belong to the AcademicYear.
     *
     * @return BelongsToMany<StudentRecord, $this, AcademicYearStudentRecord, 'studentAcademicYearBasedRecords'>
     */
    public function studentRecords(): BelongsToMany
    {
        return $this->belongsToMany(StudentRecord::class)->as('studentAcademicYearBasedRecords')->using(AcademicYearStudentRecord::class)->withPivot('my_class_id', 'section_id');
    }
}
