<?php

namespace App\Http\Requests;

use App\Models\Notice;
use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    /** Determine whether the person may create a notice. */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Notice::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'start_date' => 'date',
            'stop_date' => 'date|after:start_date',
        ];
    }
}
