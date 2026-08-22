<?php

namespace App\Http\Requests;

use App\Enums\CalendarEventType;
use App\Models\AcademicCycleSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCalendarEventRequest extends FormRequest
{
    /**
     * Determine whether the person may change this event.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('calendarEvent')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(CalendarEventType::class)],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_all_day' => ['required', 'boolean'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'academic_cycle_section_ids' => ['nullable', 'array'],
            'academic_cycle_section_ids.*' => [
                'integer',
                Rule::exists((new AcademicCycleSection)->getTable(), 'id')->where('school_id', current_school_id()),
            ],
        ];
    }

    /**
     * Get the messages for the rules that need plain wording.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ends_at.after_or_equal' => 'A day on the calendar cannot end before it starts.',
        ];
    }
}
