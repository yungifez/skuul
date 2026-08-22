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
        return [
            'homeroom_teacher_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
            'stream' => ['nullable', 'string', 'max:100'],
            'shift' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:100'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
