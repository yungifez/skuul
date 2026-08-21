<?php

namespace App\Http\Requests;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeAccountStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * The controller checks the policy against the target account.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'account_status' => ['required', Rule::enum(AccountStatus::class)->except(AccountStatus::Invited)],
            'reason'         => ['nullable', 'string', 'max:500'],
        ];
    }
}
