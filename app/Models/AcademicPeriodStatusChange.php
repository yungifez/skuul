<?php

namespace App\Models;

use App\Enums\AcademicPeriodStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RuntimeException;

/**
 * One recorded close or reopen of an academic period.
 *
 * The record is written once. A later correction is another record.
 */
class AcademicPeriodStatusChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_type',
        'period_id',
        'from_status',
        'to_status',
        'changed_by',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_status' => AcademicPeriodStatus::class,
        'to_status' => AcademicPeriodStatus::class,
    ];

    /**
     * Keep the history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Period history cannot be changed. Record the next change instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Period history cannot be deleted. Record the next change instead.');
        });
    }

    /**
     * Get the period this change belongs to.
     */
    public function period(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the person who made the change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
