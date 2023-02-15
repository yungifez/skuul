<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:10000',
            'start_date' => 'date',
            'stop_date'  => 'date|after:start_date',
        ];
    }
}
