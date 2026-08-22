<?php

namespace App\Http\Requests;

use App\Enums\SupportPlanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportPlanStatusRequest extends FormRequest
{
    /**
     * Determine whether the person may work on this plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('supportPlan')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(SupportPlanStatus::class)],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
