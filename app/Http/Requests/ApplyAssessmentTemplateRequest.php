<?php

namespace App\Http\Requests;

use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplyAssessmentTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $courseOffering = $this->route('courseOffering');

        return $courseOffering instanceof CourseOffering
            && ($this->user()?->can('manageGradebook', $courseOffering) ?? false);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'assessment_template_id' => [
                'required',
                'integer',
                Rule::exists((new AssessmentTemplate())->getTable(), 'id')->where('school_id', current_school_id())->where('is_active', true),
            ],
        ];
    }
}
