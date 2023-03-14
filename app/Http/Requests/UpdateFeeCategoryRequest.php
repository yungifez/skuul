<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeCategoryRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'school_id' => auth()->user()->school->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'school_id'   => 'required|integer|exists:schools,id',
        ];
    }
}
