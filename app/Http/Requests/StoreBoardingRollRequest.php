<?php

namespace App\Http\Requests;

use App\Enums\BoardingRollType;
use App\Models\Dormitory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBoardingRollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage boarding') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'dormitory_id' => ['required', 'integer', Rule::exists((new Dormitory)->getTable(), 'id')->where('school_id', current_school_id())],
            'type' => ['required', Rule::in(BoardingRollType::values())],
            'taken_on' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
