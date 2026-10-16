<?php

namespace App\Models;

use App\Enums\GradeItemType;
use Database\Factories\AssessmentTemplateItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentTemplateItem extends Model
{
    /** @use HasFactory<AssessmentTemplateItemFactory> */
    use HasFactory;

    protected $fillable = ['assessment_template_id', 'assessment_template_category_id', 'name', 'type', 'grading_scale_id', 'max_points', 'weight', 'position'];

    protected $attributes = ['type' => GradeItemType::Numeric->value, 'weight' => 1, 'position' => 1];

    protected $casts = ['type' => GradeItemType::class, 'max_points' => 'float', 'weight' => 'float', 'position' => 'integer'];

    /** @return BelongsTo<AssessmentTemplate, $this> */
    public function assessmentTemplate(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class);
    }

    /** @return BelongsTo<AssessmentTemplateCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplateCategory::class, 'assessment_template_category_id');
    }

    /** @return BelongsTo<GradingScale, $this> */
    public function gradingScale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class);
    }
}
