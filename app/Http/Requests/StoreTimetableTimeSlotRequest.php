<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimetableTimeSlotRequest extends FormRequest
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
            'timetable_id' => [
                'required',
                'integer',
                Rule::exists('timetables', 'id')->whereIn('my_class_id', auth()->user()->school->myClasses()->pluck('my_classes.id')),
            ],
            'start_time' => 'required|date_format:H:i',
            'stop_time'  => 'required|date_format:H:i|after:start_time',
        ];
    }
}
