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
 * @property AttendanceStatus $status
 * @property AttendanceKind   $kind
 * @property Carbon           $attended_on
 */
class AttendanceRecord extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'academic_year_id',
        'semester_id',
        'my_class_id',
        'section_id',
        'subject_id',
        'kind',
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
        'kind'   => AttendanceKind::Daily->value,
        'status' => AttendanceStatus::NotRecorded->value,
        'source' => 'teacher',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'kind'        => AttendanceKind::class,
        'status'      => AttendanceStatus::class,
        'attended_on' => 'date:Y-m-d',
        'recorded_at' => 'datetime',
    ];

    /**
     * Limit the query to one day.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeOnDate(Builder $query, DateTimeInterface|string $date): Builder
    {
        return $query->whereDate('attended_on', Carbon::parse($date)->toDateString());
    }

    /**
     * Limit the query to one register.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeOfKind(Builder $query, AttendanceKind $kind): Builder
    {
        return $query->where('kind', $kind);
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
     * Get the subject of the lesson, when the record names one.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
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
