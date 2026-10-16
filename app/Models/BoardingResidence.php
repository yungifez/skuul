<?php

namespace App\Models;

use Database\Factories\BoardingResidenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardingResidence extends Model
{
    /** @use HasFactory<BoardingResidenceFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'notes',
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

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsToMany<School, $this> */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'boarding_residence_school')
            ->withPivot('linked_by')
            ->withTimestamps();
    }

    /** @return HasMany<Dormitory, $this> */
    public function dormitories(): HasMany
    {
        return $this->hasMany(Dormitory::class);
    }
}
