<?php

namespace App\Http\Requests;

use App\Models\FinancialPeriod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFinancialPeriodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage financial period') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('financial_periods', 'name')->where(fn ($query) => $query->where('school_id', current_school_id()))],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (!$this->filled('starts_on') || !$this->filled('ends_on') || strtotime((string) $this->input('starts_on')) === false || strtotime((string) $this->input('ends_on')) === false) {
                return;
            }

            $overlaps = FinancialPeriod::query()
                ->inSchool()
                ->whereDate('starts_on', '<=', $this->input('ends_on'))
                ->whereDate('ends_on', '>=', $this->input('starts_on'))
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('starts_on', 'These dates overlap an existing financial period.');
            }
        });
    }
}
