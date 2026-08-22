<?php

namespace App\Http\Requests;

use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Models\Incident;
use App\Models\StudentRecord;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Determine whether the person may record a case.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Incident::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'summary' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(IncidentCategory::class)],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'assigned_to' => ['nullable', 'integer', $this->memberOfWorkingSchool()],
            'participants' => ['nullable', 'array', 'max:20'],
            'participants.*.student_record_id' => ['nullable', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'participants.*.role' => ['nullable', Rule::enum(IncidentParticipantRole::class)],
            'participants.*.note' => ['nullable', 'string', 'max:255'],
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
            'occurred_at.before_or_equal' => 'A case cannot be recorded for a time that has not happened.',
        ];
    }
}
