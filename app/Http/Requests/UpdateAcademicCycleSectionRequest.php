<?php

namespace App\Http\Requests;

use App\Models\AcademicCycleSection;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicCycleSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $section = $this->route('academicCycleSection');

        return $section instanceof AcademicCycleSection
            && ($this->user()?->can('update', $section) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The cycle and the academic level are not editable. A section serves one
     * exact cycle, so a later cycle needs its own section.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var AcademicCycleSection $section */
        $section = $this->route('academicCycleSection');

        return [
            'homeroom_teacher_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            // The cycle keeps one section of each name inside a level. Read the
            // cycle and the level from the record, because an edit never moves
            // a section between them.
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('academic_cycle_sections', 'name')
                    ->where('school_id', $section->school_id)
                    ->where('academic_year_id', $section->academic_year_id)
                    ->where('academic_level_id', $section->academic_level_id)
                    ->ignore($section->id),
            ],
            'label'    => ['nullable', 'string', 'max:255'],
            'stream'   => ['nullable', 'string', 'max:100'],
            'shift'    => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:100'],
            'room'     => ['nullable', 'string', 'max:100'],
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
            'name.unique' => 'That academic level already has a section with this name in this cycle. Choose another name.',
        ];
    }
}
