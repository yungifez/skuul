<?php

namespace App\Models;

use App\Enums\SchoolSetupPhaseStatus;
use App\Traits\InSchool;
use Database\Factories\SchoolSetupPhaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Tracks a versioned setup phase for one school and academic year.
 *
 * @property int $school_id
 * @property int|null $academic_year_id
 * @property string $phase_key
 * @property SchoolSetupPhaseStatus $status
 * @property Carbon|null $completed_at
 * @property Carbon|null $acknowledged_at
 * @property int|null $acknowledged_by
 */
class SchoolSetupPhase extends Model
{
    /** @use HasFactory<SchoolSetupPhaseFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'phase_key',
        'status',
        'completed_at',
        'acknowledged_at',
        'acknowledged_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => SchoolSetupPhaseStatus::class,
        'completed_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the school that owns this phase.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the academic year this phase belongs to.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the person who acknowledged the phase.
     *
     * @return BelongsTo<User, $this>
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}
