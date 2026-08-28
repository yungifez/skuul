<?php

namespace App\Http\Requests;

use App\Models\AcademicLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveAcademicLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $academicLevel = $this->route('academicLevel');

        return $academicLevel instanceof AcademicLevel
            && ($this->user()?->can('update', $academicLevel) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'direction' => ['required', Rule::in(['up', 'down'])],
        ];
    }
}
