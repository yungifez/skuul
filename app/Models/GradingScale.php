<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\GradingScaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingScale extends Model
{
    /** @use HasFactory<GradingScaleFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'is_active',
        'created_by',
    ];

    /**
     * The default values for a new scale.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the selectable levels on this scale.
     *
     * @return HasMany<GradingScaleOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(GradingScaleOption::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Get the person who first configured the scale.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the grade items using this scale.
     *
     * @return HasMany<GradeItem, $this>
     */
    public function gradeItems(): HasMany
    {
        return $this->hasMany(GradeItem::class);
    }
}
