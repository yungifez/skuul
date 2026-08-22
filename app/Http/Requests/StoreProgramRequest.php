<?php

namespace App\Http\Requests;

use App\Enums\ProgramType;
use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramRequest extends FormRequest
{
    /**
     * Determine whether the person may open a programme.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Program::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique((new Program)->getTable(), 'name')->where('school_id', current_school_id()),
            ],
            'type' => ['required', Rule::enum(ProgramType::class)],
            'description' => ['nullable', 'string', 'max:1000'],
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
            'name.unique' => 'This school already has a programme with that name.',
        ];
    }
}
