<?php

namespace App\Http\Requests;

use App\Enums\PortalRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePortalRequestRequest extends FormRequest
{
    /**
     * Determine whether the person may answer this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('answer', $this->route('portalRequest')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(PortalRequestStatus::class)],
            'response' => ['nullable', 'string', 'max:2000', 'required_if:status,'.PortalRequestStatus::Answered->value],
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
            'response.required_if' => 'An answered request must carry the answer.',
        ];
    }
}
