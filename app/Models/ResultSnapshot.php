<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * A published result, exactly as it read when it was published.
 *
 * The record is written once. A correction is the next revision, so a result
 * a family already saw never changes behind their back.
 *
 * @property array<string, mixed> $payload
 * @property int                  $revision
 * @property float|null           $percentage
 * @property int                  $course_offering_id
 */
class ResultSnapshot extends Model
{
    use HasFactory;
    use InSchool;

    /**
     * The snapshot only stores when it was written.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'course_offering_id',
        'revision',
        'percentage',
        'payload',
        'reason',
        'published_at',
        'published_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload'      => 'array',
        'percentage'   => 'float',
        'revision'     => 'integer',
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
    ];

    /**
     * Keep published results append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A published result cannot be changed. Publish the next revision instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A published result cannot be deleted. Publish the next revision instead.');
        });
    }

    /**
     * Limit the query to the newest revision of each result.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeLatestRevision(Builder $query): Builder
    {
        return $query->orderByDesc('revision')->orderByDesc('id');
    }

    /**
     * Get the enrollment the result belongs to.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the offering the result covers.
     *
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the person who published the result.
     *
     * @return BelongsTo<User, $this>
     */
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
