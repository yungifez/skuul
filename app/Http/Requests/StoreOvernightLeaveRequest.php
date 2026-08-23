<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreOvernightLeaveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'student_record_id' => [
                'required', 'integer',
                ValidationRule::exists('student_records', 'id')->where(
                    fn (Builder $query) => $query->where('school_id', current_school_id()),
                ),
            ],
            'leaves_on' => 'required|date',
            'returns_on' => 'required|date|after_or_equal:leaves_on',
            'destination' => 'required|string|max:150',
            'contact' => 'nullable|string|max:100',
            'reason' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the messages the house reads when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destination.required' => 'Say where the learner is going.',
            'returns_on.after_or_equal' => 'A learner cannot come back before they leave.',
        ];
    }
}
