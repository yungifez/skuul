<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeTimetableReord extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject_id' => 'nullable|exists:subjects,id|integer',
            'weekday_id' => 'required|exists:weekdays,id|integer',
        ];
    }
}
