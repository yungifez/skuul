<?php

namespace App\Http\Requests;

use App\Models\AcademicCycleSection;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RollForwardAcademicCycleSectionsRequest extends FormRequest
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
            'source_academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', current_school_id())],
            'target_academic_year_id' => ['required', 'integer', 'different:source_academic_year_id', Rule::exists('academic_years', 'id')->where('school_id', current_school_id())],
        ];
    }
}
