<?php

namespace App\Http\Requests;

use App\Enums\InstructionalModel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetInstructionalModelRequest extends FormRequest
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
            'reason' => ['nullable', 'string', 'max:500'],
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
            'model.required' => 'Answer the question about class groups.',
        ];
    }
}
