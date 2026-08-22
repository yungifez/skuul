<?php

namespace App\Http\Requests;

use App\Enums\DataCategory;
use App\Models\DataSharingRequest;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataSharingRequestRequest extends FormRequest
{
    /**
     * Determine whether the person may ask another school.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', DataSharingRequest::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The learner is named by admission number in one named school, never
     * chosen from a list. A school must not be able to read the roll of a
     * school it has no records from.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'holding_school_id' => ['required', 'integer', Rule::exists((new School)->getTable(), 'id')],
            'admission_number' => ['required', 'string', 'max:50'],
            'purpose' => ['required', 'string', 'max:500'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => [Rule::enum(DataCategory::class)],
            'expires_on' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get the messages for the rules that need plain wording.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'categories.required' => 'A request must name what it asks for.',
            'expires_on.after_or_equal' => 'A request cannot end before it starts.',
        ];
    }
}
