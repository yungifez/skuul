<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreLibraryCopyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'library_title_id' => 'nullable|integer|exists:library_titles,id',
            'title' => 'required_without:library_title_id|nullable|string|max:255',
            'authors' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:80',
            'published_year' => 'nullable|integer|min:1400|max:'.(int) date('Y'),
            'barcode' => [
                'required', 'string', 'max:60',
                ValidationRule::unique('library_copies', 'barcode')->where('school_id', current_school_id()),
            ],
            'shelf_mark' => 'nullable|string|max:60',
            'copies' => 'nullable|integer|min:1|max:50',
        ];
    }

    /**
     * Get the messages the library reads when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required_without' => 'Say what the book is, or pick one already in the catalogue.',
            'barcode.unique' => 'This campus already has a copy with that barcode.',
        ];
    }
}
