<?php

namespace App\Models;

use App\Enums\AdmissionWaitlistStatus;
use App\Traits\InSchool;
use Database\Factories\AdmissionWaitlistEntryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionWaitlistEntry extends Model
{
    /** @use HasFactory<AdmissionWaitlistEntryFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_cycle_section_id',
        'user_id',
        'created_by',
        'offered_by',
        'decided_by',
        'status',
        'priority',
        'position',
        'decision_reason',
        'offered_at',
        'decided_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => AdmissionWaitlistStatus::Pending->value,
        'priority' => 0,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => AdmissionWaitlistStatus::class,
        'priority' => 'integer',
        'position' => 'integer',
        'offered_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function offerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'offered_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            AdmissionWaitlistStatus::Pending->value,
            AdmissionWaitlistStatus::Offered->value,
        ]);
    }

    public function isOpen(): bool
    {
        return $this->status->isOpen();
    }
}
