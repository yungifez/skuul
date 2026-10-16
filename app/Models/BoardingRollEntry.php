<?php

namespace App\Models;

use App\Enums\BoardingRollEntryStatus;
use App\Traits\InSchool;
use Database\Factories\BoardingRollEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One boarder's answer inside a boarding roll.
 *
 * @property BoardingRollEntryStatus $status
 */
class BoardingRollEntry extends Model
{
    /** @use HasFactory<BoardingRollEntryFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'boarding_roll_id',
        'student_record_id',
        'status',
        'location',
        'note',
        'recorded_by',
        'recorded_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => BoardingRollEntryStatus::class,
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the roll this entry belongs to.
     *
     * @return BelongsTo<BoardingRoll, $this>
     */
    public function roll(): BelongsTo
    {
        return $this->belongsTo(BoardingRoll::class, 'boarding_roll_id');
    }

    /**
     * Get the boarder's enrollment.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the person who recorded the answer.
     *
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
