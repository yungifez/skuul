<?php

namespace App\Http\Requests;

use App\Models\Incident;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentNoteRequest extends FormRequest
{
    /**
     * Determine whether the person may add a note to this case.
     */
    public function authorize(): bool
    {
        $incident = $this->route('incident');

        return $incident instanceof Incident
            && ($this->user()?->can('update', $incident) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'is_restricted' => ['nullable', 'boolean'],
        ];
    }

    /**
     * A note remains private unless the writer deliberately makes it broad.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_restricted' => $this->boolean('is_restricted', true),
        ]);
    }
}
