<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();

        return [
            'role' => [
                'required',
                Rule::in($roles->pluck('id')),
            ],
            'school' => [
                'required',
                'exists:schools,id',
            ],
        ];
    }
}
