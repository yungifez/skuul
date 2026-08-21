<?php

namespace App\Models;

use App\Enums\SupportPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One recorded move of a plan between states.
 */
class SupportPlanStatusChange extends Model
{
    use HasFactory;

    /**
     * A change is written once.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'support_plan_id',
        'from_status',
        'to_status',
        'reason',
        'changed_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_status' => SupportPlanStatus::class,
        'to_status'   => SupportPlanStatus::class,
        'created_at'  => 'datetime',
    ];

    /**
     * Keep the plan history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Support plan history cannot be changed.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Support plan history cannot be deleted.');
        });
    }

    /**
     * Get the plan this change belongs to.
     *
     * @return BelongsTo<SupportPlan, $this>
     */
    public function supportPlan(): BelongsTo
    {
        return $this->belongsTo(SupportPlan::class);
    }

    /**
     * Get the person who made the change.
     *
     * @return BelongsTo<User, $this>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
