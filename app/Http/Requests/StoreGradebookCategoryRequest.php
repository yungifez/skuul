<?php

namespace App\Http\Requests;

use App\Enums\GradeAggregation;
use App\Models\CourseOffering;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradebookCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $courseOffering = $this->route('courseOffering');

        return $courseOffering instanceof CourseOffering
            && ($this->user()?->can('manageGradebook', $courseOffering) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'aggregation' => ['required', Rule::enum(GradeAggregation::class)],
            'weight' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
        ];
    }
}
