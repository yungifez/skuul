<?php

namespace App\Models;

use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One agreed plan of help for one child.
 *
 * @property SupportCategory $category
 * @property SupportPlanStatus $status
 * @property bool $is_confidential
 */
class SupportPlan extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'category',
        'status',
        'is_confidential',
        'title',
        'summary',
        'starts_on',
        'review_on',
        'ends_on',
        'academic_year_id',
        'created_by',
        'assigned_to',
    ];

    /**
     * The default values for a new plan.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'category' => SupportCategory::Intervention->value,
        'status' => SupportPlanStatus::Draft->value,
        'is_confidential' => false,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category' => SupportCategory::class,
        'status' => SupportPlanStatus::class,
        'is_confidential' => 'boolean',
        'starts_on' => 'date',
        'review_on' => 'date',
        'ends_on' => 'date',
    ];

    /**
     * A health or counselling plan is confidential, whoever writes it.
     */
    protected static function booted(): void
    {
        static::saving(function (self $plan): void {
            if ($plan->category->isConfidential()) {
                $plan->is_confidential = true;
            }
        });
    }

    /**
     * Limit the query to the plans a person may read.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeReadableBy(Builder $query, User $user): Builder
    {
        if ($user->can('read confidential support plan')) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user): void {
            $query->where('is_confidential', false)
                ->orWhere('assigned_to', $user->id)
                ->orWhere('created_by', $user->id);
        });
    }

    /**
     * Limit the query to the plans that still need work.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            SupportPlanStatus::Draft,
            SupportPlanStatus::Active,
            SupportPlanStatus::OnHold,
        ]);
    }

    /**
     * Limit the query to the plans that are due for review.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeDueForReview(Builder $query): Builder
    {
        return $query->open()->whereNotNull('review_on')->whereDate('review_on', '<=', now());
    }

    /**
     * Get the enrollment the plan is for.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the steps the school agreed to take.
     *
     * @return HasMany<SupportPlanAction, $this>
     */
    public function actions(): HasMany
    {
        return $this->hasMany(SupportPlanAction::class)->orderBy('id');
    }

    /**
     * Get what people wrote as the plan ran.
     *
     * @return HasMany<SupportPlanNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(SupportPlanNote::class)->orderBy('id');
    }

    /**
     * Get how the plan moved between states.
     *
     * @return HasMany<SupportPlanStatusChange, $this>
     */
    public function statusChanges(): HasMany
    {
        return $this->hasMany(SupportPlanStatusChange::class)->orderBy('id');
    }

    /**
     * Get the person who wrote the plan.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the person who runs the plan.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
