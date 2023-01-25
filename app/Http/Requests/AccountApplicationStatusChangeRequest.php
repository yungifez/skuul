<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountApplicationStatusChangeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        //get the applicant and set variable if user is student
        $applicant = $this->route('applicant')->load('accountApplication', 'accountApplication.role');
        $statuses = $applicant->accountApplication->getAllStatuses();
        $status = $this->input('status') ?? 'not found';
        if ($applicant->accountApplication->role->name == 'student' && $status == 'approved') {
            $applicantIsStudent = true;
            $studentStoreRequestValidationArray = [
                'admission_number' => 'nullable|unique:student_records,admission_number',
                'admission_date'   => 'required|date',
                'my_class_id'      => 'required|exists:my_classes,id',
                'section_id'       => 'required|exists:sections,id',
            ];
        } else {
            $applicantIsStudent = false;
            $studentStoreRequestValidationArray = [];
        }

        $rules = [
            'status' => [
                'required',
                Rule::in($statuses),
            ],
            'reason' => [
                'sometimes',
                'max:256',
            ],
        ];
        $rules = array_merge($rules, $studentStoreRequestValidationArray);

        return  $rules;
    }
}
