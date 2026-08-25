<?php

namespace App\Http\Requests;

use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveGradebookResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $courseOffering = $this->route('courseOffering');

        return $courseOffering instanceof CourseOffering
            && ($this->user()?->can('approveResult', $courseOffering) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'result_snapshot_id' => [
                'required',
                'integer',
                Rule::exists('result_snapshots', 'id')->where('school_id', current_school_id()),
            ],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
