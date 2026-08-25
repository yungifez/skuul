<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreLibrarySectionLoanRequest extends FormRequest
{
    /**
     * Get the rules for lending a title to a section.
     *
     * @return array<string, Rule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_cycle_section_id' => [
                'required',
                'integer',
                ValidationRule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id()),
            ],
            'library_title_id' => [
                'required',
                'integer',
                ValidationRule::exists('library_titles', 'id'),
            ],
        ];
    }
}
