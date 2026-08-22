<?php

namespace App\Http\Requests;

use App\Enums\DataSharingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataSharingRequestRequest extends FormRequest
{
    /**
     * Determine whether the person may answer this request.
     *
     * Only the school that holds the records decides, which the policy holds.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('decide', $this->route('dataSharingRequest')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(DataSharingStatus::class)],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
