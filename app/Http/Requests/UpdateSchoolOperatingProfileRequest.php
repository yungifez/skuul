<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolOperatingProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preset' => ['required', 'in:home_sections,subject_schedule,hybrid'],
            'labels' => ['required', 'array'],
            'labels.academic_year' => ['required', 'string', 'max:40'],
            'labels.class_level' => ['required', 'string', 'max:40'],
            'labels.section' => ['required', 'string', 'max:40'],
            'labels.period' => ['required', 'string', 'max:40'],
            'labels.course' => ['required', 'string', 'max:40'],
            'labels.fee' => ['required', 'string', 'max:40'],
            'labels.homeroom_teacher' => ['required', 'string', 'max:40'],
        ];
    }
}
