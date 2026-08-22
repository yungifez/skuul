<?php

namespace App\Http\Requests;

use App\Enums\Feature;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeatureSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $rules = [
            'features' => ['required', 'array:'.implode(',', Feature::values())],
        ];

        foreach (Feature::values() as $feature) {
            $rules['features.'.$feature] = ['required', 'boolean'];
        }

        return $rules;
    }
}
