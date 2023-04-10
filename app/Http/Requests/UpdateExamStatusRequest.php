<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamStatusRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->status == 'active' || $this->status == 1 || $this->status == 'on') {
            $this->merge(['status' => true]);
        } elseif ($this->status == 'inactive' || $this->status == 0 || $this->status == 'off' || $this->status == null) {
            $this->merge(['status' => false]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => 'boolean',
        ];
    }
}
