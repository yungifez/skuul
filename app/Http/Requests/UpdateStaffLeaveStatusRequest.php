<?php

namespace App\Http\Requests;

use App\Enums\LeaveStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffLeaveStatusRequest extends FormRequest
{
    /**
     * Determine whether the person may answer this request.
     *
     * Withdrawing is not a decision, so the person who asked may do it. Every
     * other move needs the permission to answer, which a person never holds
     * over their own days.
     */
    public function authorize(): bool
    {
        $leaveRequest = $this->route('staffLeaveRequest');
        $ability = $this->string('status')->toString() === LeaveStatus::Cancelled->value ? 'update' : 'decide';

        return $this->user()?->can($ability, $leaveRequest) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(LeaveStatus::class)],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
