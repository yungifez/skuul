<?php

namespace App\Models;

use App\Enums\FacilityKind;
use App\Traits\InSchool;
use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Something the school shares: a hall, a laboratory, a minibus.
 *
 * @property FacilityKind $kind
 */
class Facility extends Model
{
    /** @use HasFactory<FacilityFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'name',
        'kind',
        'capacity',
        'notes',
        'is_active',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'kind' => FacilityKind::Classroom->value,
        'is_active' => true,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'kind' => FacilityKind::class,
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get every booking ever made of this.
     *
     * @return HasMany<FacilityBooking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }

    /**
     * Limit the query to the things still in use.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Limit the query to the things a lesson can be moved into.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeHoldsLessons(Builder $query): Builder
    {
        $kinds = array_map(
            fn (FacilityKind $kind): string => $kind->value,
            array_filter(FacilityKind::cases(), fn (FacilityKind $kind): bool => $kind->holdsLessons()),
        );

        return $query->whereIn('kind', $kinds);
    }
}
