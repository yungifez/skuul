<?php

namespace App\Http\Requests;

use App\Models\StudentRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCohortMemberRequest extends FormRequest
{
    /**
     * Determine whether the person may change this group.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('cohort')) ?? false;
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
            'joined_on' => ['nullable', 'date'],
        ];
    }
}
