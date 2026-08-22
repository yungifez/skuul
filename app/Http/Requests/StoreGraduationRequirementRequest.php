<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGraduationRequirementRequest extends FormRequest
{
    /**
     * Determine whether the person may change this plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('graduationPlan')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer', Rule::exists((new Subject)->getTable(), 'id')->where('school_id', current_school_id())],
            'credits' => ['required', 'integer', 'min:0', 'max:100'],
            'pass_mark' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_required' => ['required', 'boolean'],
        ];
    }
}
