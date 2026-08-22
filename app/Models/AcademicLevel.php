<?php

namespace App\Models;

use App\Enums\AcademicStructureStatus;
use App\Traits\InSchool;
use Database\Factories\AcademicLevelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A reusable school level, such as Primary 4, Grade 4, or Form 2.
 *
 * @property AcademicStructureStatus $status
 */
class AcademicLevel extends Model
{
    /** @use HasFactory<AcademicLevelFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'parent_id',
        'name',
        'label',
        'code',
        'position',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status'   => AcademicStructureStatus::Active->value,
        'position' => 0,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'status'   => AcademicStructureStatus::class,
    ];

    /**
     * Answer whether the reusable setup of this level may still change.
     *
     * An archived level is kept for history only.
     */
    public function isEditable(): bool
    {
        return $this->status !== AcademicStructureStatus::Archived;
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<AcademicLevel, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<AcademicLevel, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('name');
    }

    /**
     * @return HasMany<AcademicCycleSection, $this>
     */
    public function cycleSections(): HasMany
    {
        return $this->hasMany(AcademicCycleSection::class);
    }
}
