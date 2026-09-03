<?php

namespace App\Http\Requests;

use App\Enums\DormitoryBedStatus;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class UpdateDormitoryBedRequest extends FormRequest
{
    /**
     * Get the rules for changing a bed.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:40',
            'status' => ['required', ValidationRule::in(DormitoryBedStatus::values())],
            'status_reason' => 'nullable|string|max:1000',
        ];
    }
}
