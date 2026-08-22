<?php

namespace App\Http\Requests;

use App\Enums\EmploymentType;
use App\Models\StaffProfile;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffProfileRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Determine whether the person may write an employment record.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', StaffProfile::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                $this->memberOfWorkingSchool(),
                // One person holds one employment record per school.
                Rule::unique((new StaffProfile)->getTable(), 'user_id')->where('school_id', current_school_id()),
            ],
            'staff_number' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'joined_on' => ['nullable', 'date'],
        ];
    }

    /**
     * Get the messages for the rules that need plain wording.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'This person already has an employment record in this school.',
        ];
    }
}
