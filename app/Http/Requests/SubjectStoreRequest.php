<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectStoreRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function subjectRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function rules(): array
    {
        return self::subjectRules();
    }
}
