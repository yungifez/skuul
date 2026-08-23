<?php

namespace App\Http\Requests;

use App\Enums\RosterMode;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class GrantOfferingExceptionRequest extends FormRequest
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
            'subject_id' => [
                'required', 'integer',
                ValidationRule::exists('subjects', 'id')->where($ofThisSchool),
            ],
            'academic_level_id' => [
                'nullable', 'integer',
                ValidationRule::exists('academic_levels', 'id')->where($ofThisSchool),
            ],
            'roster_mode' => ['required', ValidationRule::in(RosterMode::values())],
            'reason' => 'required|string|min:10|max:500',
        ];
    }

    /**
     * Get the messages staff read when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Say why this subject is taught differently.',
            'reason.min' => 'Give a reason somebody can understand next year.',
        ];
    }
}
