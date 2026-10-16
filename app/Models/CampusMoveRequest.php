<?php

namespace App\Models;

use App\Enums\CampusMoveStatus;
use Database\Factories\CampusMoveRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One campus's request to move a student to another campus.
 *
 * This model names two campuses on purpose, so it does not use the school
 * scope: the campus that receives the student decides, and the campus that
 * asked must still be able to see its own request.
 *
 * @property CampusMoveStatus $status
 * @property Carbon|null $effective_on
 * @property Carbon|null $decided_at
 */
class CampusMoveRequest extends Model
{
    /** @use HasFactory<CampusMoveRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'student_record_id',
        'from_school_id',
        'to_school_id',
        'academic_cycle_section_id',
        'status',
        'reason',
        'effective_on',
        'requested_by',
        'decided_by',
        'decided_at',
        'decision_note',
    ];

    /**
     * The default values for a new request.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => CampusMoveStatus::Requested->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CampusMoveStatus::class,
            'effective_on' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    /**
     * Limit the query to requests nobody has decided yet.
     *
     * @param  Builder<self>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->where('status', CampusMoveStatus::Requested);
    }

    /**
     * Limit the query to the requests one campus must decide.
     *
     * @param  Builder<self>  $query
     */
    public function scopeAwaiting(Builder $query, School|int $school): void
    {
        $query->where('to_school_id', $school instanceof School ? $school->id : $school)
            ->where('status', CampusMoveStatus::Requested);
    }

    /**
     * Get the enrollment that would move.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the campus the student attends now.
     *
     * @return BelongsTo<School, $this>
     */
    public function fromSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'from_school_id');
    }

    /**
     * Get the campus the student would move to.
     *
     * @return BelongsTo<School, $this>
     */
    public function toSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'to_school_id');
    }

    /**
     * Get the home section the student would take.
     *
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * Get the person who asked.
     *
     * @return BelongsTo<User, $this>
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the person who decided.
     *
     * @return BelongsTo<User, $this>
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
