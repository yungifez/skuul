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
 * A reusable school level or level group, such as Kindergarten or KG 1.
 *
 * @property AcademicStructureStatus $status
 * @property bool $is_group
 */
class AcademicLevel extends Model
{
    /** @use HasFactory<AcademicLevelFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'parent_id',
        'is_group',
        'name',
        'code',
        'position',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => AcademicStructureStatus::Active->value,
        'position' => 0,
        'is_group' => false,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_group' => 'boolean',
        'position' => 'integer',
        'status' => AcademicStructureStatus::class,
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
     * Get this level and every level nested below it.
     *
     * A whole-group offering uses these IDs to include learners from all child
     * classes without changing their individual academic-cycle placement.
     *
     * @return list<int>
     */
    public function teachingScopeIds(): array
    {
        $levels = self::inSchool($this->school_id)->get(['id', 'parent_id']);
        $childrenByParent = $levels->groupBy('parent_id');
        $ids = [$this->id];
        $pending = [$this->id];

        while ($pending !== []) {
            $parentId = array_shift($pending);

            foreach ($childrenByParent->get($parentId, collect()) as $child) {
                if (in_array($child->id, $ids, true)) {
                    continue;
                }

                $ids[] = $child->id;
                $pending[] = $child->id;
            }
        }

        return $ids;
    }

    /**
     * Get this level and every parent level above it.
     *
     * @return list<int>
     */
    public function hierarchyIds(): array
    {
        $levels = self::inSchool($this->school_id)->get(['id', 'parent_id'])->keyBy('id');
        $ids = [];
        $currentId = $this->id;

        while ($currentId !== null && !in_array($currentId, $ids, true)) {
            $ids[] = $currentId;
            $currentId = $levels->get($currentId)?->parent_id;
        }

        return $ids;
    }

    /**
     * @return HasMany<AcademicCycleSection, $this>
     */
    public function cycleSections(): HasMany
    {
        return $this->hasMany(AcademicCycleSection::class);
    }
}
