<?php

namespace App\Models;

use App\Enums\CohortType;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A named group of people that is not a class and not a section.
 *
 * @property CohortType $type
 * @property bool $is_restricted
 */
class Cohort extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'description',
        'is_restricted',
        'is_active',
        'academic_year_id',
        'created_by',
    ];

    /**
     * The default values for a new cohort.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => CohortType::Other->value,
        'is_restricted' => false,
        'is_active' => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => CohortType::class,
        'is_restricted' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * A watchlist is private, whoever makes it.
     */
    protected static function booted(): void
    {
        static::saving(function (self $cohort): void {
            if ($cohort->type->isRestricted()) {
                $cohort->is_restricted = true;
            }
        });
    }

    /**
     * Limit the query to the groups still in use.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get who is in the group.
     *
     * @return HasMany<CohortMember, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(CohortMember::class)->orderBy('id');
    }

    /**
     * Get the enrollments in the group.
     *
     * @return BelongsToMany<StudentRecord, $this>
     */
    public function studentRecords(): BelongsToMany
    {
        return $this->belongsToMany(StudentRecord::class, 'cohort_members')
            ->withPivot(['joined_on', 'left_on'])
            ->withTimestamps();
    }
}
