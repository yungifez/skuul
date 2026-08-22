<?php

namespace App\Http\Requests;

use App\Models\AcademicLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $academicLevel = $this->route('academicLevel');

        return $academicLevel instanceof AcademicLevel
            && ($this->user()?->can('update', $academicLevel) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var AcademicLevel $academicLevel */
        $academicLevel = $this->route('academicLevel');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('academic_levels', 'name')->where('school_id', current_school_id())->ignore($academicLevel->id),
            ],
            'label' => ['nullable', 'string', 'max:255'],
            'code' => [
                'nullable', 'string', 'max:100',
                Rule::unique('academic_levels', 'code')->where('school_id', current_school_id())->ignore($academicLevel->id),
            ],
            'parent_id' => [
                'nullable', 'integer',
                Rule::notIn([$academicLevel->id]),
                Rule::exists('academic_levels', 'id')->where('school_id', current_school_id()),
            ],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'parent_id.not_in' => 'An academic level cannot be its own parent.',
        ];
    }
}
