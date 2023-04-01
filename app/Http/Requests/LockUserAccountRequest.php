<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LockUserAccountRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->lock == 'active' || $this->lock == 1 || $this->lock == 'on') {
            $this->merge(['lock' => true]);
        } elseif ($this->lock == 'inactive' || $this->lock == 0 || $this->lock == 'off' || $this->lock == null) {
            $this->merge(['lock' => false]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'lock'   => 'required|boolean',
        ];
    }
}
