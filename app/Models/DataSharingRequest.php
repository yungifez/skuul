<?php

namespace App\Models;

use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One school's request to read another school's records about one student.
 *
 * This model names two schools on purpose, so it does not use the school
 * scope: the school that holds the records decides, and the school that asked
 * must still be able to see its own request.
 *
 * @property DataSharingStatus  $status
 * @property array<int, string> $categories
 */
class DataSharingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requesting_school_id',
        'holding_school_id',
        'student_record_id',
        'status',
        'categories',
        'purpose',
        'expires_on',
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
        'status' => DataSharingStatus::Requested->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'     => DataSharingStatus::class,
        'categories' => 'array',
        'expires_on' => 'date',
        'decided_at' => 'datetime',
    ];

    /**
     * Get the categories as the enum they name.
     *
     * @return array<int, DataCategory>
     */
    public function categories(): array
    {
        return array_values(array_filter(array_map(
            fn (string $value): ?DataCategory => DataCategory::tryFrom($value),
            $this->categories ?? [],
        )));
    }

    /**
     * Check if the permission ran out.
     */
    public function hasExpired(mixed $on = null): bool
    {
        if ($this->expires_on === null) {
            return false;
        }

        return $this->expires_on->lt(Carbon::parse($on ?? now())->startOfDay());
    }

    /**
     * Check if the records may be handed over now.
     */
    public function isUsable(): bool
    {
        return $this->status->allowsFulfilment() && !$this->hasExpired();
    }

    /**
     * Limit the query to the requests one school must answer.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeAwaiting(Builder $query, School|int $school): Builder
    {
        return $query->where('holding_school_id', $school instanceof School ? $school->id : $school)
            ->where('status', DataSharingStatus::Requested);
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
     * Get the school that asked.
     *
     * @return BelongsTo<School, $this>
     */
    public function requestingSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'requesting_school_id');
    }

    /**
     * Get the school that holds the records.
     *
     * @return BelongsTo<School, $this>
     */
    public function holdingSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'holding_school_id');
    }
}
