<?php

namespace App\Http\Requests;

use App\Models\Syllabus;
use App\Models\TeachingAssignment;
use Closure;
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
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'course_offering_id' => [
                'required', 'integer',
                Rule::exists('course_offerings', 'id')->where('school_id', current_school_id()),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($this->user()?->can('approve syllabus')) {
                        return;
                    }

                    if (!TeachingAssignment::query()->where('course_offering_id', $value)->where('user_id', $this->user()?->id)->exists()) {
                        $fail('You can only add a syllabus for an offering you teach.');
                    }
                },
            ],
        ];
    }
}
