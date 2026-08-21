<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'fee_category_id' => 'required|integer|exists:fee_categories,id',
            'name'            => 'required|max:1024',
            'description'     => 'nullable|max:10000',
        ];
    }
}
