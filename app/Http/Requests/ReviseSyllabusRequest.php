<?php

namespace App\Http\Requests;

use App\Models\Syllabus;
use Illuminate\Foundation\Http\FormRequest;

class ReviseSyllabusRequest extends FormRequest
{
    /**
     * Determine if the user can revise this syllabus.
     */
    public function authorize(): bool
    {
        $syllabus = $this->route('syllabus');

        return $syllabus instanceof Syllabus && ($this->user()?->can('update', $syllabus) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'change_note' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * Get the messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'change_note.required' => 'Say what will change in this revision and why.',
        ];
    }
}
