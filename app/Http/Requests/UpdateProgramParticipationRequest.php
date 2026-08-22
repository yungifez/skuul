<?php

namespace App\Http\Requests;

use App\Enums\ParticipationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramParticipationRequest extends FormRequest
{
    /**
     * Determine whether the person may change this programme.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('program')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(ParticipationStatus::class)],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
