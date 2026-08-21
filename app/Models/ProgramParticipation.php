<?php

namespace App\Models;

use App\Enums\ParticipationStatus;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person's place in one programme.
 *
 * Taking part is not enrollment. A student can be in several programmes while
 * enrolled once.
 *
 * @property ParticipationStatus $status
 */
class ProgramParticipation extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'program_id',
        'student_record_id',
        'status',
        'starts_on',
        'ends_on',
        'schedule',
        'note',
        'staff_id',
        'academic_year_id',
    ];

    /**
     * The default values for a new place.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ParticipationStatus::Requested->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ParticipationStatus::class,
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    /**
     * Limit the query to the places that are still held.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->whereIn('status', [ParticipationStatus::Requested, ParticipationStatus::Active]);
    }

    /**
     * Get the programme.
     *
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the enrollment taking part.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the member of staff who runs it for this person.
     *
     * @return BelongsTo<User, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
