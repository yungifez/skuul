<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One correction of an attendance record.
 *
 * Marking the wrong child absent matters to a family, so the first answer and
 * every correction stay side by side.
 */
class AttendanceChange extends Model
{
    use HasFactory;

    /**
     * A correction is written once.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'attendance_record_id',
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
        'from_status' => AttendanceStatus::class,
        'to_status'   => AttendanceStatus::class,
        'created_at'  => 'datetime',
    ];

    /**
     * Keep the history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Attendance history cannot be changed. Record the next correction instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Attendance history cannot be deleted.');
        });
    }

    /**
     * Get the record this correction belongs to.
     *
     * @return BelongsTo<AttendanceRecord, $this>
     */
    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    /**
     * Get the person who made the correction.
     *
     * @return BelongsTo<User, $this>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
