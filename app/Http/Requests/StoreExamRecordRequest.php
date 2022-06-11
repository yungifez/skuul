<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRecordRequest extends FormRequest
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
        //max validation for marks is handled in service class as there is no clean way of doing this
        return [
            'section_id'   => 'required|integer|exists:sections,id',
            'subject_id'   => 'required|integer|exists:subjects,id',
            'user_id'      => 'required|integer|exists:users,id',
            'exam_records' => 'array',
            //validates to check if each exam record has a student_marks and exam_slot_id
            'exam_records.*.exam_slot_id'  => 'required|integer|exists:exam_slots,id',
            'exam_records.*.student_marks' => 'required|integer|min:0',
        ];
    }
}
