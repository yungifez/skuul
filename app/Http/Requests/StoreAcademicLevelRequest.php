<?php

namespace App\Http\Requests;

use App\Models\AcademicLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', AcademicLevel::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('academic_levels', 'name')->where('school_id', current_school_id())],
            'code' => ['nullable', 'string', 'max:100', Rule::unique('academic_levels', 'code')->where('school_id', current_school_id())],
            'is_group' => ['sometimes', 'boolean'],
            'parent_id' => ['nullable', 'integer', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())->where('is_group', true)],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
