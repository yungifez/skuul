<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimetableRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (is_null($this->input('type')) || !in_array($this->input('type'), ['subject', 'customTimetableItem'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->input('type') == 'subject') {
            return [
                'type' => [
                    'required',
                    Rule::in(['subject', 'customTimetableItem']),
                ],
                'id' => [
                    'nullable',
                    'integer',
                    'exists:subjects,id',
                ],
                'weekday_id' => 'required|exists:weekdays,id|integer',
            ];
        } elseif ($this->input('type') == 'customTimetableItem') {
            return [
                'type' => [
                    'required',
                    Rule::in(['subject', 'customTimetableItem']),
                ],
                'id' => [
                    'nullable',
                    'integer',
                    'exists:custom_timetable_items,id',
                ],
                'weekday_id' => 'required|exists:weekdays,id|integer',
            ];
        } else {
            $this->merge(['id' => null]);

            return [
                'type' => [
                    'required',
                    Rule::in(['subject', 'customTimetableItem']),
                ],
                'id' => [
                    'nullable',
                    'integer',
                    'exists:custom_timetable_items,id',
                ],
                'weekday_id' => 'required|exists:weekdays,id|integer',
            ];
        }
    }
}
