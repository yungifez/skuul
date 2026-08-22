<?php

namespace App\Http\Requests;

use App\Enums\PortalRequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePortalRequestRequest extends FormRequest
{
    /**
     * Determine whether the person may send a request.
     *
     * The action checks that this person may ask about this student, and that
     * the school takes requests at all, so the rule here stays about the form.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(PortalRequestType::class)],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
