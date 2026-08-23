<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreBoardingPlaceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $ofThisSchool = fn (Builder $query) => $query->where('school_id', current_school_id());

        return [
            'student_record_id' => [
                'required', 'integer',
                ValidationRule::exists('student_records', 'id')->where($ofThisSchool),
            ],
            'dormitory_bed_id' => [
                'required', 'integer',
                ValidationRule::exists('dormitory_beds', 'id')->where($ofThisSchool),
            ],
            'reason' => 'nullable|string|max:255',
            'effective_on' => 'nullable|date',
        ];
    }
}
