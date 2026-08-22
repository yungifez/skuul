<?php

namespace App\Http\Requests;

use App\Models\StudentHealthRecord;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentHealthRecordRequest extends FormRequest
{
    /**
     * Determine whether the person may write a health record.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', StudentHealthRecord::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'blood_group' => ['nullable', 'string', 'max:10'],
            'conditions' => ['nullable', 'string', 'max:2000'],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'medications' => ['nullable', 'string', 'max:2000'],
            'dietary_needs' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
