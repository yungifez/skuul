<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One step of a support plan.
 *
 * @property bool $is_done
 */
class SupportPlanAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_plan_id',
        'description',
        'due_on',
        'completed_at',
        'assigned_to',
        'completed_by',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_on'       => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Check if the step is finished.
     */
    public function isDone(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Check if the step is late.
     */
    public function isLate(): bool
    {
        return !$this->isDone() && $this->due_on !== null && $this->due_on->isPast();
    }

    /**
     * Get the plan this step belongs to.
     *
     * @return BelongsTo<SupportPlan, $this>
     */
    public function supportPlan(): BelongsTo
    {
        return $this->belongsTo(SupportPlan::class);
    }

    /**
     * Get the person who must do it.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
