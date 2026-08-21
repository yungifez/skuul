<?php

namespace App\Models;

use App\Enums\LeaveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One recorded move of a leave request between states.
 */
class StaffLeaveStatusChange extends Model
{
    use HasFactory;

    /**
     * A change is written once.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'staff_leave_request_id',
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
        'from_status' => LeaveStatus::class,
        'to_status' => LeaveStatus::class,
        'created_at' => 'datetime',
    ];

    /**
     * Keep the leave history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Leave history cannot be changed.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Leave history cannot be deleted.');
        });
    }

    /**
     * Get the request this change belongs to.
     *
     * @return BelongsTo<StaffLeaveRequest, $this>
     */
    public function staffLeaveRequest(): BelongsTo
    {
        return $this->belongsTo(StaffLeaveRequest::class);
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
