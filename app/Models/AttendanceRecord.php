<?php

namespace App\Models;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Traits\InSchool;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Whether one student was in school on one day, or in one lesson.
 *
 * A lesson record names the course offering it was taken for, and the lesson
 * time when one was chosen, so two lessons of one subject on a day each keep
 * their own register.
 *
 * @property AttendanceStatus $status
 * @property AttendanceKind $kind
 * @property Carbon $attended_on
 * @property string $register_key
 * @property int|null $course_offering_id
 * @property int|null $timetable_time_slot_id
 */
class AttendanceRecord extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'academic_year_id',
        'academic_period_id',
        'academic_cycle_section_id',
        'subject_id',
        'course_offering_id',
        'timetable_time_slot_id',
        'kind',
        'register_key',
        'attended_on',
        'status',
        'reason',
        'source',
        'recorded_by',
        'recorded_at',
    ];

    /**
     * The default values for a new record.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'kind' => AttendanceKind::Daily->value,
        'status' => AttendanceStatus::NotRecorded->value,
        'source' => 'teacher',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'kind' => AttendanceKind::class,
        'status' => AttendanceStatus::class,
        'attended_on' => 'date:Y-m-d',
        'recorded_at' => 'datetime',
    ];

    /**
     * Name the register a new record belongs to.
     *
     * One student holds one record in each register on a day.
     */
    protected static function booted(): void
    {
        static::saving(function (self $record): void {
            if (blank($record->register_key)) {
                $record->register_key = self::registerKey($record->kind, $record->course_offering_id, $record->timetable_time_slot_id, $record->subject_id);
            }
        });
    }

    /**
     * Get the key of one register.
     *
     * The daily register is one per day. A lesson register is one per
     * offering, or per offering and lesson time when a time was chosen.
     */
    public static function registerKey(AttendanceKind $kind, ?int $courseOfferingId = null, ?int $timeSlotId = null, ?int $subjectId = null): string
    {
        if ($kind === AttendanceKind::Daily) {
            return 'daily';
        }

        if ($courseOfferingId !== null) {
            return 'offering:'.$courseOfferingId.($timeSlotId === null ? '' : ':slot:'.$timeSlotId);
        }

        return 'subject:'.$subjectId;
    }

    /**
     * Limit the query to one day.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOnDate(Builder $query, DateTimeInterface|string $date): Builder
    {
        return $query->whereDate('attended_on', Carbon::parse($date)->toDateString());
    }

    /**
     * Limit the query to one register.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOfKind(Builder $query, AttendanceKind $kind): Builder
    {
        return $query->where('kind', $kind);
    }

    /**
     * Get the campus whose register holds this record.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the enrollment this record belongs to.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the exact home section where this register was taken.
     *
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * Get the subject of the lesson, when the record names one.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the course offering of the lesson, when the record names one.
     *
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the person who took the register.
     *
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get every correction made to this record.
     *
     * @return HasMany<AttendanceChange, $this>
     */
    public function changes(): HasMany
    {
        return $this->hasMany(AttendanceChange::class)->orderBy('id');
    }
}
