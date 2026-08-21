<?php

namespace App\Http\Requests;

use App\Services\Import\ImportRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImportBatchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(app(ImportRegistry::class)->all()))],
            'file' => ['required', 'file', 'mimetypes:text/plain,text/csv,application/csv', 'max:5120'],
        ];
    }
}
