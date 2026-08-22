<?php

namespace App\Http\Requests;

use App\Enums\LeaveType;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffLeaveRequest extends FormRequest
{
    /**
     * Determine whether the person may ask for leave.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', StaffLeaveRequest::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'staff_profile_id' => ['required', 'integer', Rule::exists((new StaffProfile)->getTable(), 'id')->where('school_id', current_school_id())],
            'type' => ['required', Rule::enum(LeaveType::class)],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'reason' => ['nullable', 'string', 'max:500'],
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
            'ends_on.after_or_equal' => 'Leave cannot end before it starts.',
        ];
    }
}
