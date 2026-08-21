<?php

namespace App\Models;

use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Something a student or guardian asked the school for.
 *
 * @property PortalRequestType $type
 * @property PortalRequestStatus $status
 */
class PortalRequest extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'requested_by',
        'type',
        'status',
        'subject',
        'message',
        'response',
        'answered_by',
        'answered_at',
    ];

    /**
     * The default values for a new request.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => PortalRequestType::Document->value,
        'status' => PortalRequestStatus::Submitted->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => PortalRequestType::class,
        'status' => PortalRequestStatus::class,
        'answered_at' => 'datetime',
    ];

    /**
     * Limit the query to the requests still waiting for the school.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [PortalRequestStatus::Submitted, PortalRequestStatus::InReview]);
    }

    /**
     * Get the enrollment the request is about.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
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
     * Get the person who answered.
     *
     * @return BelongsTo<User, $this>
     */
    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }
}
