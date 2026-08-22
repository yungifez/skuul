<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportPlanNoteRequest extends FormRequest
{
    /**
     * Determine whether the person may work on this plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('supportPlan')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
