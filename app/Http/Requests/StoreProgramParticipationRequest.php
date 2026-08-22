<?php

namespace App\Http\Requests;

use App\Models\StudentRecord;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramParticipationRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Determine whether the person may change this programme.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('program')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_record_id' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'starts_on' => ['nullable', 'date'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'staff_id' => [
                'nullable',
                'integer',
                $this->memberOfWorkingSchool(),
            ],
        ];
    }
}
