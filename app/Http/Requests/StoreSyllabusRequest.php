<?php

namespace App\Http\Requests;

use App\Models\Syllabus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSyllabusRequest extends FormRequest
{
    /** Determine if the user can create a syllabus. */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Syllabus::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10000'],
            'course_offering_id' => ['required', 'integer', Rule::exists('course_offerings', 'id')->where('school_id', current_school_id())],
        ];
    }
}
