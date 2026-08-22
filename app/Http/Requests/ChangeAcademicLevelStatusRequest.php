<?php

namespace App\Http\Requests;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeAcademicLevelStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(AcademicStructureStatus::class)],
        ];
    }
}
