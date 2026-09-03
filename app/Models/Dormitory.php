<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\DormitoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * A boarding house on one campus.
 *
 * Schools do not agree on the word. The record stays a dormitory and the
 * screens read the campus label, so a school that says "hostel" is not made
 * to say "dormitory".
 */
class Dormitory extends Model
{
    /** @use HasFactory<DormitoryFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'boarding_residence_id',
        'name',
        'label',
        'notes',
        'is_active',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'label' => 'House',
        'is_active' => true,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the campus the house belongs to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /** @return BelongsTo<BoardingResidence, $this> */
    public function boardingResidence(): BelongsTo
    {
        return $this->belongsTo(BoardingResidence::class);
    }

    /**
     * Get the rooms in the house.
     *
     * @return HasMany<DormitoryRoom, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(DormitoryRoom::class);
    }

    /**
     * Get every bed in the house.
     *
     * @return HasManyThrough<DormitoryBed, DormitoryRoom, $this>
     */
    public function beds(): HasManyThrough
    {
        return $this->hasManyThrough(DormitoryBed::class, DormitoryRoom::class);
    }

    /**
     * Get the staff on duty here, past and present.
     *
     * @return HasMany<BoardingSupervision, $this>
     */
    public function supervisions(): HasMany
    {
        return $this->hasMany(BoardingSupervision::class);
    }

    /**
     * Limit the query to the houses still in use.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
