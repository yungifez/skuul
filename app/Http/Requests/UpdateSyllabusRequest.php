<?php

namespace App\Http\Requests;

use App\Models\Syllabus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSyllabusRequest extends FormRequest
{
    /**
     * Determine if the user can change this syllabus.
     */
    public function authorize(): bool
    {
        $syllabus = $this->route('syllabus');

        return $syllabus instanceof Syllabus && ($this->user()?->can('update', $syllabus) ?? false);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'remove_file' => ['sometimes', 'boolean'],
        ];
    }
}
