<?php

namespace App\Http\Requests;

use App\Models\StudentRecord;
use App\Models\TranscriptSnapshot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTranscriptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', TranscriptSnapshot::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return self::transcriptRules();
    }

    /** @return array<string, array<int, mixed>> */
    public static function transcriptRules(): array
    {
        return [
            'student_record_id' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
