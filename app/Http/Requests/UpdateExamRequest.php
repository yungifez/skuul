<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'academic_period_id' => [
                'required',
                'integer',
                Rule::exists('academic_periods', 'id')->where(
                    fn ($query) => $query->where('school_id', current_school_id()),
                ),
            ],
            'start_date' => 'required|date',
            'stop_date' => 'required|date|after_or_equal:start_date',
        ];
    }
}
