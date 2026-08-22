<?php

namespace App\Http\Requests;

use App\Models\AcademicPeriod;
use App\Models\ReportCardSnapshot;
use App\Models\StudentRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', ReportCardSnapshot::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'student_record_id' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'academic_period_id' => ['required', 'integer', Rule::exists((new AcademicPeriod)->getTable(), 'id')->where('school_id', current_school_id())],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
