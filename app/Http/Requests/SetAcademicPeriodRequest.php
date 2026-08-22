<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetAcademicPeriodRequest extends FormRequest
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
            'academic_period_id' => 'required|exists:academic_periods,id',
        ];
    }
}
