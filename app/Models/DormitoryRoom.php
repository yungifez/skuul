<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\DormitoryRoomFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One room inside a boarding house.
 */
class DormitoryRoom extends Model
{
    /** @use HasFactory<DormitoryRoomFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'dormitory_id',
        'name',
        'floor',
        'is_active',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the house the room is in.
     *
     * @return BelongsTo<Dormitory, $this>
     */
    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class);
    }

    /**
     * Get the beds in the room.
     *
     * @return HasMany<DormitoryBed, $this>
     */
    public function beds(): HasMany
    {
        return $this->hasMany(DormitoryBed::class);
    }

    /**
     * Limit the query to the rooms still in use.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
