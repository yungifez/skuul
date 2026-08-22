<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use App\Models\AcademicCycleSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('take attendance') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'academic_cycle_section_id' => ['required', 'integer', Rule::exists((new AcademicCycleSection())->getTable(), 'id')->where('school_id', current_school_id())],
            'attended_on'               => ['required', 'date', 'before_or_equal:today'],
            'statuses'                  => ['required', 'array'],
            'statuses.*'                => ['required', Rule::in(AttendanceStatus::values())],
        ];
    }
}
