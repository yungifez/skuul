<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreFacilityBookingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'facility_id' => [
                'required', 'integer',
                ValidationRule::exists('facilities', 'id')->where(
                    fn (Builder $query) => $query->where('school_id', current_school_id()),
                ),
            ],
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'purpose' => 'required|string|max:255',
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
            'ends_at.after' => 'A booking has to end after it starts.',
            'purpose.required' => 'Say what the booking is for.',
        ];
    }
}
