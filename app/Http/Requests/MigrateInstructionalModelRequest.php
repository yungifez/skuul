<?php

namespace App\Http\Requests;

use App\Enums\InstructionalModel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MigrateInstructionalModelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'model' => ['required', Rule::enum(InstructionalModel::class)],
            'reason' => ['required', 'string', 'min:15', 'max:500'],
            'confirm' => ['accepted'],
        ];
    }

    /**
     * Get the messages a person reads when the form is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'model.required' => 'Choose the model this cycle is moving to.',
            'reason.required' => 'Say why this cycle is moving mid-year.',
            'reason.min' => 'Write the reason in a full sentence, so it reads later.',
            'confirm.accepted' => 'Confirm that you understand what changes for staff.',
        ];
    }
}
