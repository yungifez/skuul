<?php

namespace App\Http\Requests;

use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssessmentTemplateRequest extends FormRequest
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
            'template_name' => ['required', 'string', 'max:150', Rule::unique('assessment_templates', 'name')->where('school_id', current_school_id())],
            'description'   => ['nullable', 'string', 'max:5000'],
        ];
    }
}
