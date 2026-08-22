<?php

namespace App\Http\Requests;

use App\Models\AcademicCycleSection;
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
     */
    public function rules(): array
    {
        return [
            'timetable_id' => [
                'required',
                'integer',
                Rule::exists('timetables', 'id')->whereIn(
                    'academic_cycle_section_id',
                    AcademicCycleSection::inSchool()->pluck('id')
                ),
            ],
            'start_time' => 'required|date_format:H:i',
            'stop_time'  => 'required|date_format:H:i|after:start_time',
        ];
    }
}
