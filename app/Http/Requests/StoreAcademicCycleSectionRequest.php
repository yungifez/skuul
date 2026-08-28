<?php

namespace App\Http\Requests;

use App\Models\AcademicCycleSection;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicCycleSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', AcademicCycleSection::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', current_school_id())],
            'academic_level_id' => ['required', 'integer', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())->where('is_group', false)],
            'homeroom_teacher_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            // A cycle keeps one section of each name inside a level, which the
            // `academic_cycle_sections_identity_unique` index enforces. Catch
            // it here so a repeated name reads as a message, not a 500.
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('academic_cycle_sections', 'name')
                    ->where('school_id', current_school_id())
                    ->where('academic_year_id', $this->integer('academic_year_id'))
                    ->where('academic_level_id', $this->integer('academic_level_id')),
            ],
            'label' => ['nullable', 'string', 'max:255'],
            'stream' => ['nullable', 'string', 'max:100'],
            'shift' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:100'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This class already has a section with this name in this school year. Choose another name.',
        ];
    }
}
