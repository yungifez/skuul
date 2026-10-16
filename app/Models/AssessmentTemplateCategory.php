<?php

namespace App\Models;

use App\Enums\GradeAggregation;
use Database\Factories\AssessmentTemplateCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentTemplateCategory extends Model
{
    /** @use HasFactory<AssessmentTemplateCategoryFactory> */
    use HasFactory;

    protected $fillable = ['assessment_template_id', 'parent_id', 'name', 'aggregation', 'weight', 'position'];

    protected $attributes = ['aggregation' => GradeAggregation::WeightedMean->value, 'weight' => 1, 'position' => 1];

    protected $casts = ['aggregation' => GradeAggregation::class, 'weight' => 'float', 'position' => 'integer'];

    /** @return BelongsTo<AssessmentTemplate, $this> */
    public function assessmentTemplate(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class);
    }

    /** @return BelongsTo<AssessmentTemplateCategory, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<AssessmentTemplateItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(AssessmentTemplateItem::class)->orderBy('position')->orderBy('id');
    }
}
