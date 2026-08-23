<?php

namespace App\Http\Requests;

use App\Traits\ValidatesSchoolMembership;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignCampusRoleRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // A role is campus work, so it only reaches somebody who works here.
            'user_id' => ['required', $this->memberOfWorkingSchool()],
        ];
    }
}
