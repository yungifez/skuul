<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One recorded change of an enrollment state.
 *
 * The record is written once. Correcting history means adding the next
 * change, never editing or deleting an old one.
 */
class EnrollmentStatusChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_record_id',
        'from_status',
        'to_status',
        'effective_on',
        'changed_by',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_status'  => EnrollmentStatus::class,
        'to_status'    => EnrollmentStatus::class,
        'effective_on' => 'date:Y-m-d',
    ];

    /**
     * Keep the history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Enrollment history cannot be changed. Record the next change instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Enrollment history cannot be deleted. Record the next change instead.');
        });
    }

    /**
     * Get the enrollment this change belongs to.
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the person who made the change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
