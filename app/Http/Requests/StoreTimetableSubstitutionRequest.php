<?php

namespace App\Http\Requests;

use App\Models\Timetable;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableSubstitutionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $timetable = $this->route('timetable');

        return $timetable instanceof Timetable && ($this->user()?->can('substitute', $timetable) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'timetable_entry' => ['required', 'string', 'regex:/^\\d+:\\d+$/'],
            'replacement_teacher_id' => ['required', 'integer', 'exists:users,id'],
            'substituted_on' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
