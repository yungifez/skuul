<?php

namespace App\Models;

use Database\Factories\AssessmentTemplateApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentTemplateApplication extends Model
{
    /** @use HasFactory<AssessmentTemplateApplicationFactory> */
    use HasFactory;

    protected $fillable = ['assessment_template_id', 'course_offering_id', 'applied_by', 'applied_at'];

    protected $casts = ['applied_at' => 'datetime'];

    /** @return BelongsTo<AssessmentTemplate, $this> */
    public function assessmentTemplate(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class);
    }

    /** @return BelongsTo<CourseOffering, $this> */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /** @return BelongsTo<User, $this> */
    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
