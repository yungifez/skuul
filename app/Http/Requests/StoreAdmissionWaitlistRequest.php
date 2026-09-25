<?php

namespace App\Http\Requests;

use App\Models\AdmissionWaitlistEntry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdmissionWaitlistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', AdmissionWaitlistEntry::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return self::waitlistRulesForWorkingSchool();
    }

    /** @return array<string, array<int, mixed>> */
    public static function waitlistRulesForWorkingSchool(): array
    {
        return [
            'academic_cycle_section_id' => [
                'required',
                'integer',
                Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id()),
            ],
            'user_id' => ['required', 'integer', Rule::exists('school_memberships', 'user_id')->where('school_id', current_school_id())],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
