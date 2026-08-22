<?php

namespace App\Http\Requests;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeAcademicCycleSectionStatusRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(AcademicStructureStatus::class)],
        ];
    }
}
