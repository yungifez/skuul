<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicYearStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_year' => 'required|digits:4|integer|min:1900',
            'stop_year'  => 'required|digits:4|integer|min:1900|gt:start_year',
        ];
    }
}
