<?php

namespace App\Http\Requests;

use App\Enums\GradingScaleType;
use App\Models\GradingScale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateGradingScaleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'scale_type' => $this->input('scale_type', GradingScaleType::Points->value),
        ]);
    }

    public function authorize(): bool
    {
        $scale = $this->route('gradingScale');

        return $scale instanceof GradingScale && ($this->user()?->can('update', $scale) ?? false);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        /** @var GradingScale|null $scale */
        $scale = $this->route('gradingScale');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('grading_scales', 'name')->where('school_id', current_school_id())->ignore($scale)],
            'description' => ['nullable', 'string', 'max:5000'],
            'scale_type' => ['required', Rule::enum(GradingScaleType::class)],
            'maximum_value' => [Rule::requiredIf(fn (): bool => $this->input('scale_type') === GradingScaleType::Gpa->value), 'nullable', 'numeric', 'gt:0', 'max:999999.99'],
            'is_active' => ['nullable', 'boolean'],
            'options' => ['required', 'array'],
            'options.*.id' => ['nullable', 'integer', Rule::exists('grading_scale_options', 'id')],
            'options.*.label' => ['nullable', 'string', 'max:100', 'required_with:options.*.points'],
            'options.*.points' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $options = collect($this->input('options', []))
                ->filter(fn (mixed $option): bool => is_array($option) && trim((string) Arr::get($option, 'label', '')) !== '');

            if ($options->count() < 2) {
                $validator->errors()->add('options', 'Keep at least two grade options.');
            }

            if ($options->pluck('label')->map(fn (string $label): string => mb_strtolower(trim($label)))->unique()->count() !== $options->count()) {
                $validator->errors()->add('options', 'Each grade option needs a different label.');
            }

            $withPoints = $options->filter(fn (array $option): bool => Arr::get($option, 'points') !== null && Arr::get($option, 'points') !== '');

            $scaleType = GradingScaleType::tryFrom((string) $this->input('scale_type'));

            if ($scaleType === null) {
                return;
            }

            if ($scaleType === GradingScaleType::Descriptive && !$withPoints->isEmpty()) {
                $validator->errors()->add('options', 'Descriptive scales must leave recorded values blank.');
            }

            if ($scaleType->usesNumericValues() && $scaleType !== GradingScaleType::Points && $withPoints->count() !== $options->count()) {
                $validator->errors()->add('options', 'Enter a numeric value for every percentage, GPA, or custom-point option.');
            }

            if ($scaleType === GradingScaleType::Percentage && $withPoints->contains(fn (array $option): bool => (float) Arr::get($option, 'points') > 100)) {
                $validator->errors()->add('options', 'Percentage values must be between 0 and 100.');
            }

            if ($scaleType === GradingScaleType::Gpa && $this->filled('maximum_value') && $withPoints->contains(fn (array $option): bool => (float) Arr::get($option, 'points') > (float) $this->input('maximum_value'))) {
                $validator->errors()->add('options', 'GPA values cannot be higher than the maximum GPA.');
            }

            if ($scaleType === GradingScaleType::Points && !$withPoints->isEmpty() && $withPoints->count() !== $options->count()) {
                $validator->errors()->add('options', 'Either give every grade option points or leave points blank for all of them.');
            }
        }];
    }
}
