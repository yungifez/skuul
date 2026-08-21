<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One dated note about how a plan is going.
 */
class SupportPlanNote extends Model
{
    use HasFactory;

    /**
     * A note is written once.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'support_plan_id',
        'body',
        'written_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Keep the record of what was said append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A support note cannot be changed.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A support note cannot be deleted.');
        });
    }

    /**
     * Get the plan the note belongs to.
     *
     * @return BelongsTo<SupportPlan, $this>
     */
    public function supportPlan(): BelongsTo
    {
        return $this->belongsTo(SupportPlan::class);
    }

    /**
     * Get the person who wrote it.
     *
     * @return BelongsTo<User, $this>
     */
    public function writtenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'written_by');
    }
}
