<?php

namespace App\Http\Requests;

use App\Traits\ValidatesSchoolMembership;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLibraryReservationRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'library_title_id' => [
                'required',
                'integer',
                Rule::exists('library_titles', 'id'),
            ],
            // The library lends to this campus, so it queues for it too.
            'user_id' => ['required', $this->memberOfWorkingSchool()],
        ];
    }
}
