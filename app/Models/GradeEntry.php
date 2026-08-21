<?php

namespace App\Models;

use App\Enums\GradeEntryState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * What one student got for one grade item.
 *
 * The state says what happened. A missing entry and an excused entry are
 * different facts, and neither is a silent zero.
 *
 * @property GradeEntryState $state
 * @property float|null $points
 */
class GradeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_item_id',
        'student_record_id',
        'state',
        'points',
        'scale_value',
        'comment',
        'graded_by',
        'graded_at',
    ];

    /**
     * The default values for a new entry.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'state' => GradeEntryState::Graded->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'state' => GradeEntryState::class,
        'points' => 'float',
        'graded_at' => 'datetime',
    ];

    /**
     * Get the item this mark belongs to.
     *
     * @return BelongsTo<GradeItem, $this>
     */
    public function gradeItem(): BelongsTo
    {
        return $this->belongsTo(GradeItem::class);
    }

    /**
     * Get the enrollment this mark belongs to.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the person who entered the mark.
     *
     * @return BelongsTo<User, $this>
     */
    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
