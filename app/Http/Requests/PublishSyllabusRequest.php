<?php

namespace App\Http\Requests;

use App\Models\Syllabus;
use Illuminate\Foundation\Http\FormRequest;

class PublishSyllabusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [];
    }
}
