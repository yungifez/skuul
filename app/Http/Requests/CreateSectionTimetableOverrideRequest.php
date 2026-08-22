<?php

namespace App\Http\Requests;

use App\Models\Timetable;
use Illuminate\Foundation\Http\FormRequest;

class CreateSectionTimetableOverrideRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $timetable = $this->route('timetable');

        return $timetable instanceof Timetable && ($this->user()?->can('update', $timetable) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return ['academic_cycle_section_id' => ['required', 'integer']];
    }
}
