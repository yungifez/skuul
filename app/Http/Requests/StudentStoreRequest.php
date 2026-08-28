<?php

namespace App\Http\Requests;

use App\Enums\AcademicStructureStatus;
use Illuminate\Validation\Rule;

class StudentStoreRequest extends StoreUserRequest
{
    protected function prepareForValidation(): void
    {
        $admissionDate = $this->input('admission_date');

        if (!is_string($admissionDate) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $admissionDate)) {
            return;
        }

        foreach (['m/d/Y', 'n/j/Y', 'd/m/Y', 'j/n/Y', 'm/d/y', 'n/j/y', 'd/m/y', 'j/n/y'] as $format) {
            $date = \DateTimeImmutable::createFromFormat('!'.$format, $admissionDate);
            $errors = \DateTimeImmutable::getLastErrors();

            if ($date !== false
                && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                && $date->format($format) === $admissionDate) {
                $this->merge(['admission_date' => $date->format('Y-m-d')]);

                return;
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'admission_number' => [
                'nullable',
                Rule::unique('student_records', 'admission_number')->where(fn ($query) => $query->where('school_id', current_school_id())),
            ],
            'admission_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'academic_cycle_section_id' => [
                'required',
                'integer',
                Rule::exists('academic_cycle_sections', 'id')
                    ->where('school_id', current_school_id())
                    ->where('academic_year_id', current_academic_year_id())
                    ->where('status', AcademicStructureStatus::Active->value),
            ],
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'academic_cycle_section_id.required' => 'Select a '.strtolower(school_term('section', 'section')),
            'admission_date.date_format' => 'Choose a valid admission date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'academic_cycle_section_id' => strtolower(school_term('section', 'section')),
        ];
    }
}
