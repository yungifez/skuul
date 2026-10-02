<?php

namespace App\Models;

use App\Enums\ProgramType;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Something the school runs beside its lessons.
 *
 * @property ProgramType $type
 */
class Program extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'description',
        'is_active',
    ];

    /**
     * The default values for a new programme.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => ProgramType::Club->value,
        'is_active' => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ProgramType::class,
        'is_active' => 'boolean',
    ];

    /**
     * Limit the query to the programmes still running.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get who takes part.
     *
     * @return HasMany<ProgramParticipation, $this>
     */
    public function participations(): HasMany
    {
        return $this->hasMany(ProgramParticipation::class)->orderBy('id');
    }
}
