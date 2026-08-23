<?php

namespace App\Models;

use App\Enums\InstructionalModel;
use App\Traits\InSchool;
use Database\Factories\InstructionalModelMigrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One recorded move of a running cycle to another instructional model.
 *
 * The record is written once and answers "who moved this cycle, when, why,
 * and what the cycle held at that moment". A later correction is another
 * record.
 *
 * @property InstructionalModel|null $from_model
 * @property InstructionalModel $to_model
 * @property string $reason
 * @property array<string, mixed>|null $impact
 * @property int $school_id
 * @property int $academic_year_id
 */
class InstructionalModelMigration extends Model
{
    /** @use HasFactory<InstructionalModelMigrationFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'from_model',
        'to_model',
        'reason',
        'impact',
        'migrated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_model' => InstructionalModel::class,
        'to_model' => InstructionalModel::class,
        'impact' => 'array',
    ];

    /**
     * Keep the history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Instructional model history cannot be changed. Record the next move instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Instructional model history cannot be deleted. Record the next move instead.');
        });
    }

    /**
     * Get the campus this move belongs to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the cycle that was moved.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the person who moved the cycle.
     *
     * @return BelongsTo<User, $this>
     */
    public function migratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'migrated_by');
    }
}
